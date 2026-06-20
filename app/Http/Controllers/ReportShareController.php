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
        $secretary = auth()->user();
        abort_unless($report->secretary_id === $secretary->id, 403);

        $destinationSecretaries = Secretary::where('is_active', true)
            ->where('id', '!=', $secretary->id)
            ->orderBy('name')
            ->get();

        return view('secretary.share.create', [
            'report' => $report,
            'destinationSecretaries' => $destinationSecretaries,
            'history' => $report->shares()->with(['fromSecretary', 'toSecretary'])->latest()->get(),
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
            'shared_at' => now(),
        ]);

        $toSecretary = Secretary::findOrFail($validated['to_secretary_id']);

        try {
            $toSecretary->notify(new ReportSharedWithSecretary($share));
        } catch (\Throwable $throwable) {
            Log::error('Erro ao notificar secretaria compartilhada: ' . $throwable->getMessage());
        }

         return redirect()
            ->route('secretary.share.index')
            ->with(
            'success',
            "Denúncia #{$report->id} compartilhada com {$toSecretary->name} com sucesso."
        );
    }

    public function index()
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = auth()->user();

        $reports = Report::where('secretary_id', $secretary->id)
            ->latest()
            ->get();

        return view('secretary.share.index', [
            'secretary' => $secretary,
            'reports' => $reports,
        ]);
    }

}