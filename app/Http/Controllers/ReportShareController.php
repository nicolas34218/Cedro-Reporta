<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportShare;
use App\Models\ReportHistory;
use App\Models\Admin; 
use App\Http\Requests\StoreReportShareRequest;
use App\Http\Requests\RespondReportShareRequest;
use App\Notifications\ReportSharedWithSecretary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;

class ReportShareController extends Controller
{
    /**
     * Compartilha uma denúncia com outra secretaria.
     */
    public function store(StoreReportShareRequest $request, $id): JsonResponse
    {
        $report = Report::findOrFail($id);
        $user = Auth::user(); // Usuário logado
        
        $data = $request->validated();

        try {
            DB::transaction(function () use ($report, $user, $data) {
                // 1. Salvar o compartilhamento
                $share = ReportShare::create([
                    'report_id' => $report->id,
                    'source_secretary_id' => $report->secretary_id, // Secretaria atual da denúncia
                    'destination_secretary_id' => $data['destination_secretary_id'],
                    'user_id' => $user->id,
                    'status' => 'PENDENTE',
                    'justification' => $data['justification'],
                ]);

                // 2. Registrar o histórico
                $this->recordHistory(
                    $report->id,
                    $user->id,
                    'COMPARTILHAMENTO_CRIADO',
                    $data['justification'],
                    $report->secretary_id,
                    $data['destination_secretary_id']
                );

                // 3. Notificar todos os administradores da secretaria destino
                $destinationAdmins = Admin::where('secretary_id', $data['destination_secretary_id'])->get();
                if ($destinationAdmins->isNotEmpty()) {
                    Notification::send($destinationAdmins, new ReportSharedWithSecretary($share));
                }
            });

            return response()->json([
                'message' => 'Denúncia compartilhada com sucesso. Aguardando aceite da secretaria destino.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao compartilhar denúncia.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Aceita um compartilhamento pendente.
     */
    public function accept(RespondReportShareRequest $request, $id): JsonResponse
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
                    'status' => 'ACEITO'
                ]);

                // 2. Registrar no histórico
                $this->recordHistory(
                    $share->report_id,
                    Auth::id(),
                    'COMPARTILHAMENTO_ACEITO',
                    'A secretaria de destino aceitou o compartilhamento.',
                    $share->source_secretary_id,
                    $share->destination_secretary_id
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
    public function reject(RespondReportShareRequest $request, $id): JsonResponse
    {
        $share = ReportShare::findOrFail($id);

        // Verifica a Policy: Somente administradores da secretaria destino podem rejeitar
        $this->authorize('respond', $share);

        if ($share->status !== 'PENDENTE') {
            return response()->json(['error' => 'Este compartilhamento não está pendente.'], 422);
        }

        $data = $request->validated();

        try {
            DB::transaction(function () use ($share, $data) {
                // 1. Alterar status para REJEITADO e salvar justificativa
                $share->update([
                    'status' => 'REJEITADO',
                    'response_justification' => $data['justification']
                ]);

                // 2. Registrar no histórico
                $this->recordHistory(
                    $share->report_id,
                    Auth::id(),
                    'COMPARTILHAMENTO_REJEITADO',
                    $data['justification'],
                    $share->source_secretary_id,
                    $share->destination_secretary_id
                );
            });

            return response()->json([
                'message' => 'Compartilhamento rejeitado com sucesso.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao rejeitar compartilhamento.', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Método auxiliar privado para padronizar o registro de histórico.
     * Adapte os nomes das colunas de acordo com a sua tabela 'report_histories'.
     */
    private function recordHistory($reportId, $userId, $action, $justification, $sourceId = null, $destinationId = null): void
    {
        ReportHistory::create([
            'report_id' => $reportId,
            'user_id' => $userId,
            'action' => $action,
            'justification' => $justification,
            'source_secretary_id' => $sourceId,
            'destination_secretary_id' => $destinationId,
        ]);
    }
}