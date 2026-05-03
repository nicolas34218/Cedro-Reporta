<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use App\Traits\FormatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controlador de denúncias/relatórios.
 *
 * Gerencia a criação, visualização e rastreamento de denúncias no sistema.
 * Implementa os critérios de aceitação para as três funcionalidades:
 * 1. Registrar Denúncia
 * 2. Visualizar Denúncia
 * 3. Acompanhar Status da Denúncia
 */
class ReportController extends Controller
{
    use FormatReport;

    /**
     * Exibe o formulário de criação de denúncia.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('citizen.reports.create');
    }

    /**
     * Registra uma nova denúncia no sistema.
     *
     * Critérios de Aceitação:
     * - A descrição deve ter no mínimo 10 caracteres ✓
     * - Todas as informações obrigatórias da denúncia devem estar corretamente
     *   preenchidas antes do envio ✓
     * - O status inicial da denúncia deve ser sempre definido como "Pendente"
     *   pelo sistema ✓
     *
     * @param ReportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReportRequest $request)
    {
        try {
            // Obtém os dados validados e sanitizados do request
            $validated = $request->validated();

            // Constrói a localização a partir de endereço e bairro
            $location = null;
            if (!empty($validated['address_reference']) || !empty($validated['district'])) {
                $parts = array_filter([
                    $validated['address_reference'] ?? '',
                    $validated['district'] ?? ''
                ]);
                $location = implode(' - ', $parts);
            }

            // Cria a denúncia com o usuário autenticado
            $report = auth()->user()->reports()->create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'status' => ReportStatus::PENDING, // Status inicial sempre é "Pendente"
                'location' => $location,
            ]);

            // Log de sucesso
            Log::info('Denúncia criada com sucesso', [
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'category' => $report->category,
            ]);

            return redirect()
                ->route('citizen.reports.index')
                ->with('success', 'Denúncia registrada com sucesso! ID: #' . $report->id);
        } catch (\Exception $e) {
            // Log de erro detalhado (sem expor informações sensíveis ao usuário)
            Log::error('Erro ao criar denúncia', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao registrar a denúncia. Tente novamente.');
        }
    }

    /**
     * Lista todas as denúncias do usuário autenticado.
     *
     * Critérios de Aceitação:
     * - As denúncias mais recentes devem aparecer primeiro ✓
     * - Deve mostrar resumo (título, status e data) ✓
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtém todas as denúncias do usuário, ordenadas por mais recentes
        $reports = auth()->user()->reports()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('citizen.reports.index', [
            'reports' => $reports,
        ]);
    }

    /**
     * Exibe os detalhes de uma denúncia específica.
     *
     * Critério de Aceitação:
     * - O usuário só pode visualizar suas próprias denúncias ✓
     *
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function show(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('view', $report);

        return view('citizen.reports.show', [
            'report' => $report,
        ]);
    }

    /**
     * Exibe a página de acompanhamento de status da denúncia.
     *
     * Critérios de Aceitação:
     * - O usuário só pode visualizar suas próprias denúncias ✓
     * - O status deve ser exibido de forma destacada (ex: cores ou ícones) ✓
     *
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function trackStatus(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('track', $report);

        return view('citizen.reports.track-status', [
            'report' => $report,
        ]);
    }
}
