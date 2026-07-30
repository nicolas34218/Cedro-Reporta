<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Report;
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

        // 1. Mostrar APENAS compartilhamentos pendentes na tela de "Aceitar/Rejeitar"
        $incomingShares = $secretary->receivedShares()
            ->with(['report', 'fromSecretary', 'toSecretary'])
            ->where('status', 'pending') // <-- Filtro adicionado: Apenas pendentes
            ->latest('shared_at')
            ->get();

        // 2. Mostrar na lista geral APENAS denúncias que a secretaria é dona
        // OU que o compartilhamento foi ACEITO.
        $reports = Report::where('secretary_id', $secretary->id)
            ->orWhereHas('shares', function ($query) use ($secretary) {
                $query->where('to_secretary_id', $secretary->id)
                      ->where('status', 'accepted'); // <-- Filtro adicionado: Apenas aceitos
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

        // 1. Validação de segurança original do seu projeto
        abort_unless($report->isResponsibleSecretary($secretary), 403);

        // 2. Lógica correta para definir se a secretaria é dona da denúncia
        $isOwner = $report->secretary_id === $secretary->id;

        // 3. Busca das secretarias destino respeitando a lógica original
        $destinationSecretaries = $isOwner
            ? Secretary::where('is_active', true)
                ->where('id', '!=', $secretary->id)
                ->orderBy('name')
                ->get()
            : collect();

        // 4. Busca do histórico utilizando a relação nativa (substituindo a falha da IA)
        $historyEntries = $report->shares()
            ->with(['fromSecretary', 'toSecretary'])
            ->latest('shared_at')
            ->get();

        // 5. Retorno limpo para a view
        return view('secretary.share.create', [
            'report'                 => $report,
            'isOwner'                => $isOwner,
            'destinationSecretaries' => $destinationSecretaries,
            'history'                => $historyEntries, // Aponta para os mesmos dados
            'historyEntries'         => $historyEntries, // Mantém para retrocompatibilidade com a sua view
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
                // 1. Salvar o compartilhamento (já funciona como histórico)
                $share = ReportShare::create([
                    'report_id' => $report->id,
                    'from_secretary_id' => $secretary->id,
                    'to_secretary_id' => $data['to_secretary_id'],
                    'message' => $data['message'] ?? null,
                    'status' => 'pending',
                    'shared_at' => now(),
                ]);

                // 2. Notificar APENAS a secretaria de destino
                Notification::send($destinationSecretary, new \App\Notifications\ReportSharedWithSecretary($share));
            });

            return redirect()
                ->route('secretary.reports.show', $report)
                ->with('success', 'Denúncia compartilhada com sucesso. Aguardando aceite da secretaria destino.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erro ao compartilhar denúncia.');
        }
    }

    /**
     * Envia uma atualização/mensagem sobre uma denúncia compartilhada.
     */
    public function postUpdate(Request $request, Report $report)
    {
        /** @var \App\Models\Secretary $secretary */
        $secretary = auth()->user();

        // 1. Valida se a secretaria tem permissão para atuar na denúncia
        abort_unless($report->isResponsibleSecretary($secretary), 403);

        // 2. Valida o texto enviado pelo formulário com o nome correto ('content')
        $data = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        try {
            // 3. Cria o registro isolado de histórico
            \App\Models\ReportHistory::create([
                'report_id'   => $report->id,
                'action'      => 'Atualização',
                'actor_name'  => $secretary->name,
                'actor_role'  => 'Secretaria',
                'description' => $data['content'],
            ]);

            return redirect()
                ->route('secretary.reports.show', $report)
                ->with('success', 'Feedback registrado com sucesso na linha do tempo!');

        } catch (\Exception $e) {
            dd(['mensagem' => $e->getMessage(), 'arquivo' => $e->getFile(), 'linha' => $e->getLine()]);
        }
    }

    /**
     * Aceita o compartilhamento de uma denúncia.
     */
    public function accept(Request $request, ReportShare $share)
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        abort_unless($share->to_secretary_id == $secretary->id, 403);

        // NOVA TRAVA: Só permite aceitar se estiver pendente
        abort_if($share->status !== 'pending', 422, 'Este compartilhamento já foi respondido e não pode ser alterado.');

        try {
            DB::transaction(function () use ($share) {
                $share->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                ]);
            });

            return redirect()
                ->route('secretary.reports.show', $share->report_id)
                ->with('success', 'Compartilhamento aceito. Você agora também é responsável por esta denúncia.');

        } catch (\Exception $e) {
            dd(['mensagem' => $e->getMessage(), 'arquivo' => $e->getFile(), 'linha' => $e->getLine()]);
        }
    }

    /**
     * Rejeita o compartilhamento de uma denúncia.
     */
    public function reject(Request $request, ReportShare $share)
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        abort_unless($share->to_secretary_id == $secretary->id, 403);

        // NOVA TRAVA: Só permite rejeitar se estiver pendente
        abort_if($share->status !== 'pending', 422, 'Este compartilhamento já foi respondido e não pode ser alterado.');

        $data = $request->validate([
            'response' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            DB::transaction(function () use ($share, $data) {
                $share->update([
                    'status' => 'rejected',
                    'response' => $data['response'] ?? null,
                    'responded_at' => now(),
                ]);
            });

            return back()->with('success', 'Compartilhamento rejeitado com sucesso.');

        } catch (\Exception $e) {
            dd(['mensagem' => $e->getMessage(), 'arquivo' => $e->getFile(), 'linha' => $e->getLine()]);
        }
    }

}