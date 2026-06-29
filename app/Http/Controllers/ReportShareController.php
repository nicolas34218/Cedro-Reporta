<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportShare;
use App\Models\Secretary;
use App\Notifications\ReportSharedWithSecretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ReportShareController extends Controller
{
    public function create(Report $report)
    {
        /** @var Secretary $secretary */
        $secretary = auth()->user();
        abort_unless($report->isResponsibleSecretary($secretary), 403);

        $isOwner = $report->secretary_id === $secretary->id;

        $destinationSecretaries = $isOwner
            ? Secretary::where('is_active', true)
                ->where('id', '!=', $secretary->id)
                ->orderBy('name')
                ->get()
            : collect();

        return view('secretary.share.create', [
            'report' => $report,
            'isOwner' => $isOwner,
            'destinationSecretaries' => $destinationSecretaries,
            'history' => $report->shares()->with(['fromSecretary', 'toSecretary'])->latest()->get(),
            'historyEntries' => $report->histories,
        ]);
    }

    public function store(Request $request, Report $report)
    {
        $secretary = auth()->user();
        abort_unless($report->secretary_id === $secretary->id, 403);

        $validated = $request->validate([
            'to_secretary_id' => [
                'required',
                Rule::exists('secretaries', 'id')->where('is_active', true),
                Rule::notIn([$secretary->id]),
            ],
            'message' => ['nullable', 'string', 'max:1000'],
        ], [
            'to_secretary_id.required' => 'Selecione a secretaria de destino.',
            'to_secretary_id.exists' => 'Secretaria de destino inválida.',
            'to_secretary_id.not_in' => 'Não é possível compartilhar com a própria secretaria.',
            'message.max' => 'A observação deve ter no máximo 1000 caracteres.',
        ]);

        $alreadyShared = $report->shares()
            ->where('to_secretary_id', $validated['to_secretary_id'])
            ->exists();

        if ($alreadyShared) {
            return back()->with('error', 'Esta denúncia já foi compartilhada com esta secretaria.');
        }

        $share = ReportShare::create([
            'report_id' => $report->id,
            'from_secretary_id' => $secretary->id,
            'to_secretary_id' => $validated['to_secretary_id'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
            'shared_at' => now(),
        ]);

        $toSecretary = Secretary::findOrFail($validated['to_secretary_id']);

        \App\Models\ReportHistory::log(
            $report,
            'Compartilhada com outra secretaria',
            "Denúncia compartilhada com a secretaria \"{$toSecretary->name}\"."
                . (!empty($validated['message']) ? " Observação: {$validated['message']}" : '')
        );

        try {
            $toSecretary->notify(new ReportSharedWithSecretary($share));
        } catch (\Throwable $throwable) {
            Log::error('Erro ao notificar secretaria compartilhada: ' . $throwable->getMessage());
        }

         return redirect()
            ->route('secretary.reports.show', $report)
            ->with(
            'success',
            "Denúncia #{$report->id} compartilhada com {$toSecretary->name} com sucesso."
        );
    }

    /**
     * Registra uma atualização manual sobre o andamento da denúncia,
     * disponível tanto para a secretaria responsável atual quanto para
     * qualquer secretaria com quem a denúncia tenha sido compartilhada.
     */
    public function postUpdate(Request $request, Report $report)
    {
        /** @var Secretary $secretary */
        $secretary = auth()->user();
        abort_unless($report->isResponsibleSecretary($secretary), 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'content.required' => 'Descreva a atualização sobre a denúncia.',
            'content.min' => 'A atualização deve ter no mínimo 5 caracteres.',
            'content.max' => 'A atualização deve ter no máximo 1000 caracteres.',
        ]);

        \App\Models\ReportHistory::log(
            $report,
            'Atualização sobre a denúncia',
            $validated['content']
        );

        return back()->with('success', 'Atualização registrada com sucesso.');
    }

    public function accept(ReportShare $share)
    {
        /** @var Secretary $secretary */
        $secretary = auth()->user();

        abort_unless(
            $share->to_secretary_id === $secretary->id,
            403
        );

        $share->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);

        \App\Models\ReportHistory::log(
            $share->report,
            'Compartilhamento aceito',
            "A secretaria \"{$secretary->name}\" aceitou o compartilhamento da denúncia."
        );

        return back()->with(
            'success',
            'Compartilhamento aceito com sucesso.'
        );
    }

    public function reject(Request $request, ReportShare $share)
    {
        /** @var Secretary $secretary */
        $secretary = auth()->user();

        abort_unless(
            $share->to_secretary_id === $secretary->id,
            403
        );

        $validated = $request->validate([
            'response' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ]
        ]);

        $share->update([
            'status' => 'rejected',
            'response' => $validated['response'],
            'responded_at' => now(),
        ]);

        \App\Models\ReportHistory::log(
            $share->report,
            'Compartilhamento recusado',
            "A secretaria \"{$secretary->name}\" recusou o compartilhamento. Justificativa: {$validated['response']}"
        );

        return back()->with(
            'success',
            'Compartilhamento recusado.'
        );
    }

    public function index()
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = auth()->user();

        $reports = Report::where('secretary_id', $secretary->id)
            ->latest()
            ->get();

        $incomingShares = ReportShare::with([
        'report',
        'fromSecretary'
            ])
            ->where('to_secretary_id', $secretary->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('secretary.share.index', [
            'secretary'      => $secretary,
            'reports'        => $reports,
            'incomingShares' => $incomingShares,
        ]);
    }

}