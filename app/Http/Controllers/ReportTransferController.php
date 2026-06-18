<?php

namespace App\Http\Controllers;

use App\Enums\TransferStatus;
use App\Models\Report;
use App\Models\ReportTransfer;
use App\Models\Secretary;
use App\Notifications\ReportTransferRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Controla o encaminhamento de denúncias entre secretarias.
 */
class ReportTransferController extends Controller
{
    /**
     * Lista os encaminhamentos pendentes recebidos e as denúncias da secretaria
     * que podem ser transferidas para outro setor.
     */
    public function index()
    {
        $secretary = auth()->user();

        $incomingTransfers = $secretary->incomingTransfers()
            ->pending()
            ->with(['report', 'fromSecretary'])
            ->latest()
            ->get();

        $transferableReports = Report::where('secretary_id', $secretary->id)
            ->whereDoesntHave('transfers', fn ($query) => $query->where('status', TransferStatus::PENDING))
            ->orderByDesc('created_at')
            ->get();

        return view('secretary.transfer.transfer_reports', [
            'incomingTransfers' => $incomingTransfers,
            'transferableReports' => $transferableReports,
        ]);
    }

    /**
     * Exibe o formulário de transferência de uma denúncia específica.
     */
    public function create(Report $report)
    {
        $secretary = auth()->user();
        abort_unless($report->secretary_id === $secretary->id, 403);

        if ($report->transfers()->pending()->exists()) {
            return redirect()->route('secretary.transfer.index')
                ->with('error', 'Esta denúncia já possui uma transferência pendente de avaliação.');
        }

        $destinationSecretaries = Secretary::where('is_active', true)
            ->where('id', '!=', $secretary->id)
            ->orderBy('name')
            ->get();

        return view('secretary.transfer.create', [
            'report' => $report,
            'destinationSecretaries' => $destinationSecretaries,
            'history' => $report->transfers()->with(['fromSecretary', 'toSecretary'])->get(),
        ]);
    }

    /**
     * Registra o encaminhamento da denúncia para outra secretaria, com justificativa
     * obrigatória. A responsabilidade só passa para o destino quando ele aceitar.
     */
    public function store(Request $request, Report $report)
    {
        $secretary = auth()->user();
        abort_unless($report->secretary_id === $secretary->id, 403);

        if ($report->transfers()->pending()->exists()) {
            return back()->with('error', 'Esta denúncia já possui uma transferência pendente de avaliação.');
        }

        $validated = $request->validate([
            'to_secretary_id' => [
                'required',
                Rule::exists('secretaries', 'id')->where('is_active', true),
                Rule::notIn([$secretary->id]),
            ],
            'justification' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'to_secretary_id.required' => 'Selecione a secretaria de destino.',
            'to_secretary_id.exists' => 'Secretaria de destino inválida.',
            'to_secretary_id.not_in' => 'Não é possível transferir a denúncia para a própria secretaria.',
            'justification.required' => 'Informe a justificativa da transferência.',
            'justification.min' => 'A justificativa deve ter no mínimo 10 caracteres.',
            'justification.max' => 'A justificativa deve ter no máximo 1000 caracteres.',
        ]);

        $transfer = ReportTransfer::create([
            'report_id' => $report->id,
            'from_secretary_id' => $secretary->id,
            'to_secretary_id' => $validated['to_secretary_id'],
            'justification' => $validated['justification'],
            'status' => TransferStatus::PENDING,
        ]);

        $toSecretary = Secretary::find($validated['to_secretary_id']);

        try {
            $toSecretary->notify(new ReportTransferRequested($transfer));
            $toSecretary->creator?->notify(new ReportTransferRequested($transfer));
        } catch (\Exception $e) {
            Log::error('Erro ao notificar secretaria de destino sobre transferência: ' . $e->getMessage());
        }

        return redirect()->route('secretary.transfer.index')
            ->with('success', "Denúncia #{$report->id} encaminhada para {$toSecretary->name} com sucesso!");
    }

    /**
     * A secretaria de destino aceita o encaminhamento: a responsabilidade
     * pela denúncia passa a ser dela a partir deste momento.
     */
    public function accept(ReportTransfer $transfer)
    {
        $secretary = auth()->user();
        abort_unless($transfer->to_secretary_id === $secretary->id, 403);

        if (!$transfer->isPending()) {
            return back()->with('error', 'Esta transferência já foi avaliada.');
        }

        $transfer->update([
            'status' => TransferStatus::ACCEPTED,
            'decided_at' => now(),
        ]);

        $transfer->report()->update(['secretary_id' => $transfer->to_secretary_id]);

        return redirect()->route('secretary.transfer.index')
            ->with('success', "Denúncia #{$transfer->report_id} aceita e transferida para sua secretaria.");
    }

    /**
     * A secretaria de destino rejeita o encaminhamento, exigindo justificativa.
     * A denúncia permanece sob responsabilidade da secretaria de origem.
     */
    public function reject(Request $request, ReportTransfer $transfer)
    {
        $secretary = auth()->user();
        abort_unless($transfer->to_secretary_id === $secretary->id, 403);

        if (!$transfer->isPending()) {
            return back()->with('error', 'Esta transferência já foi avaliada.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Informe o motivo da rejeição.',
            'rejection_reason.min' => 'O motivo deve ter no mínimo 10 caracteres.',
            'rejection_reason.max' => 'O motivo deve ter no máximo 1000 caracteres.',
        ]);

        $transfer->update([
            'status' => TransferStatus::REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
            'decided_at' => now(),
        ]);

        return redirect()->route('secretary.transfer.index')
            ->with('success', 'Encaminhamento rejeitado. A denúncia permanece com a secretaria de origem.');
    }
}
