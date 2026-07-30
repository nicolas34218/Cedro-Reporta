<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Models\ReportShare;
use App\Models\Secretary;
use App\Notifications\ReportSharedWithSecretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;

class ReportShareController extends Controller
{
    /**
     * Exibe as denúncias da secretaria autenticada e os compartilhamentos recebidos.
     */
    public function index()
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        $incomingShares = $secretary->receivedShares()
            ->with(['report', 'fromSecretary', 'toSecretary'])
            ->latest('shared_at')
            ->get();

        $reports = Report::where('secretary_id', $secretary->id)
            ->orWhereHas('shares', function ($query) use ($secretary) {
                $query->where('to_secretary_id', $secretary->id);
            })
            ->with(['citizen', 'secretary'])
            ->latest('created_at')
            ->get();

        return view('secretary.share.index', [
            'incomingShares' => $incomingShares,
            'reports' => $reports,
        ]);
    }

    /**
     * Exibe a tela de compartilhamento de uma denúncia específica.
     */
    public function create(Report $report)
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        abort_unless($report->isResponsibleSecretary($secretary), 403);

        $isOwner = $report->secretary_id === $secretary->id;

        $destinationSecretaries = $isOwner
            ? Secretary::where('is_active', true)
                ->where('id', '!=', $secretary->id)
                ->orderBy('name')
                ->get()
            : collect();

        $history = $report->shares()
            ->with(['fromSecretary', 'toSecretary'])
            ->latest('shared_at')
            ->get();

        $historyEntries = $report->histories()
            ->latest()
            ->get();

        return view('secretary.share.create', [
            'report' => $report,
            'isOwner' => $isOwner,
            'destinationSecretaries' => $destinationSecretaries,
            'history' => $history,
            'historyEntries' => $historyEntries,
        ]);
    }

    /**
     * Compartilha uma denúncia com outra secretaria.
     */
    public function store(Request $request, Report $report)
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        abort_unless($report->secretary_id === $secretary->id, 403);

        $data = $request->validate([
            'to_secretary_id' => ['required', 'integer', 'exists:secretaries,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if((int) $data['to_secretary_id'] === $secretary->id, 422, 'Não é possível compartilhar a denúncia com a própria secretaria.');

        abort_if(
            ReportShare::where('report_id', $report->id)
                ->where('to_secretary_id', $data['to_secretary_id'])
                ->exists(),
            422,
            'Já existe um compartilhamento para esta secretaria.'
        );

        $destinationSecretary = Secretary::findOrFail($data['to_secretary_id']);

        try {
            DB::transaction(function () use ($report, $secretary, $data, $destinationSecretary) {
                // 1. Salvar o compartilhamento
                $share = ReportShare::create([
                    'report_id' => $report->id,
                    'from_secretary_id' => $secretary->id,
                    'to_secretary_id' => $data['to_secretary_id'],
                    'message' => $data['message'] ?? null,
                    'status' => 'pending',
                    'shared_at' => now(),
                ]);

                // 2. Registrar o histórico
                ReportHistory::log(
                    $report,
                    'Compartilhada com outra secretaria',
                    'Compartilhada com a secretaria "' . $destinationSecretary->name . '".' .
                        (!empty($data['message']) ? ' Observação: ' . $data['message'] : '')
                );

                // 3. Notificar a secretaria de destino e seus administradores
                Notification::send($destinationSecretary, new ReportSharedWithSecretary($share));

                $destinationAdmins = Admin::where('secretary_id', $data['to_secretary_id'])->get();
                if ($destinationAdmins->isNotEmpty()) {
                    Notification::send($destinationAdmins, new ReportSharedWithSecretary($share));
                }
            });

            return redirect()
                ->route('secretary.reports.show', $report)
                ->with('success', 'Denúncia compartilhada com sucesso. Aguardando aceite da secretaria destino.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao compartilhar denúncia.');
        }
    }

    /**
     * Registra uma atualização manual sobre o andamento da denúncia.
     */
    public function postUpdate(Request $request, Report $report)
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        abort_unless($report->isResponsibleSecretary($secretary), 403);

        $data = $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        ReportHistory::log(
            $report,
            'Atualização sobre a denúncia',
            $data['content']
        );

        return back()->with('success', 'Atualização registrada com sucesso.');
    }

    /**
     * Aceita um compartilhamento pendente.
     */
    public function accept(Request $request, $id): JsonResponse
    {
        $share = ReportShare::findOrFail($id);

        // Verifica a Policy: Somente administradores da secretaria destino podem aceitar
        $this->authorize('respond', $share);

        if ($share->status !== 'PENDENTE') {
            return response()->json(['error' => 'Este compartilhamento não está pendente.'], 422);
        }

        try {
            DB::transaction(function () use ($share) {
                // 1. Alterar status para ACEITO
                $share->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                ]);

                // 2. Registrar no histórico
                ReportHistory::log(
                    $share->report,
                    'Compartilhamento aceito',
                    'A secretaria de destino aceitou o compartilhamento.'
                );

                // Nota: A secretaria destino já é tratada como responsável devido 
                // à relação `sharedSecretaries()` configurada na Model Report.
            });

            return response()->json([
                'message' => 'Compartilhamento aceito com sucesso. Sua secretaria agora também é responsável pela denúncia.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao aceitar compartilhamento.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Rejeita um compartilhamento pendente.
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $share = ReportShare::findOrFail($id);

        // Verifica a Policy: Somente administradores da secretaria destino podem rejeitar
        $this->authorize('respond', $share);

        if ($share->status !== 'PENDENTE') {
            return response()->json(['error' => 'Este compartilhamento não está pendente.'], 422);
        }

        $data = $request->validate([
            'justification' => ['required', 'string', 'min:5'],
        ]);

        try {
            DB::transaction(function () use ($share, $data) {
                // 1. Alterar status para REJEITADO e salvar justificativa
                $share->update([
                    'status' => 'rejected',
                    'response' => $data['justification'],
                    'responded_at' => now(),
                ]);

                // 2. Registrar no histórico
                ReportHistory::log(
                    $share->report,
                    'Compartilhamento rejeitado',
                    $data['justification']
                );
            });

            return response()->json([
                'message' => 'Compartilhamento rejeitado com sucesso.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao rejeitar compartilhamento.', 'details' => $e->getMessage()], 500);
        }
    }

}