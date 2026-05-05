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
     * - Deve exibir todos os dados completos da denúncia ✓
     * - Deve mostrar data e hora de criação da denúncia ✓
     *
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function show(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('view', $report);

        // Carrega o usuário relacionado
        $report->load('user');

        // Usa o trait para formatar a denúncia
        $reportFormatted = $this->formatReport($report, includeDescription: true);

        return view('citizen.reports.show', [
            'report' => $report,
            'reportFormatted' => $reportFormatted,
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

    /**
     * Retorna os detalhes de uma denúncia em formato JSON (API).
     *
     * Critérios de Aceitação:
     * - O usuário só pode acessar suas próprias denúncias ✓
     * - Deve retornar todos os dados completos da denúncia ✓
     * - Deve incluir data e hora de criação formatadas ✓
     *
     * @param Report $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('view', $report);

        // Carrega o usuário relacionado
        $report->load('user');

        // Usa o trait para formatar a denúncia
        $reportFormatted = $this->formatReport($report, includeDescription: true);

        return response()->json([
            'success' => true,
            'message' => 'Detalhes da denúncia obtidos com sucesso.',
            'data' => [
                'report' => $reportFormatted,
                'user' => [
                    'id' => $report->user->id,
                    'name' => $report->user->name,
                    'email' => $report->user->email,
                ],
                'full_data' => [
                    'id' => $report->id,
                    'image_path' => $report->image_path,
                    'created_at_iso' => $report->created_at->toIso8601String(),
                    'updated_at_iso' => $report->updated_at->toIso8601String(),
                    'timestamp_created' => $report->created_at->timestamp,
                ],
            ],
        ]);
    }

    /**
     * Exibe página de busca e filtro de denúncias.
     *
     * Critérios de Aceitação:
     * - O usuário só vê suas próprias denúncias ✓
     * - Permite filtro por categoria ✓
     * - Permite filtro por localização ✓
     * - Permite filtro por status ✓
     * - Permite combinar filtros simultaneamente ✓
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        // Constrói a query base com as denúncias do usuário
        $query = auth()->user()->reports();

        // Busca por termo livre (título, descrição, localização)
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }

        // Aplica filtros da request (categoria / localização / status)
        $filters = [
            'category' => $request->input('category'),
            'location' => $request->input('location'),
            'status' => $request->input('status'),
        ];

        $query->filter($filters);

        // Obtém as denúncias filtradas, ordenadas por mais recentes
        $reports = $query->orderByDesc('created_at')->paginate(10);

        // Retorna para a mesma view de listagem com filtros aplicados
        return view('citizen.reports.index', [
            'reports' => $reports,
            'filters' => $filters,
            'categories' => $this->getAvailableCategories(),
            'statuses' => $this->getAvailableStatuses(),
        ]);
    }

    /**
     * Endpoint de API para filtrar denúncias.
     *
     * Critérios de Aceitação:
     * - O usuário só vê suas próprias denúncias ✓
     * - Retorna dados em formato JSON ✓
     * - Permite combinar múltiplos filtros ✓
     * - Inclui metadados de paginação ✓
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        // Validação dos parâmetros de filtro
        $validated = $request->validate([
            'category' => 'nullable|string',
            'location' => 'nullable|string|min:1',
            'status' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Define paginação
        $perPage = $validated['per_page'] ?? 10;

        // Constrói a query base com as denúncias do usuário
        $query = auth()->user()->reports();

        // Aplica filtros
        $filters = [
            'category' => $validated['category'] ?? null,
            'location' => $validated['location'] ?? null,
            'status' => $validated['status'] ?? null,
        ];

        $query->filter($filters);

        // Obtém as denúncias filtradas com paginação
        $reports = $query->orderByDesc('created_at')->paginate($perPage);

        // Formata os dados das denúncias
        $formattedReports = $reports->map(fn($report) => $this->formatReport($report, includeDescription: false));

        return response()->json([
            'success' => true,
            'message' => 'Filtros aplicados com sucesso.',
            'data' => [
                'reports' => $formattedReports,
                'pagination' => [
                    'total' => $reports->total(),
                    'per_page' => $reports->perPage(),
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'from' => $reports->firstItem(),
                    'to' => $reports->lastItem(),
                ],
                'filters_applied' => array_filter($filters),
            ],
        ]);
    }

    /**
     * Retorna as categorias disponíveis para filtro.
     *
     * @return array
     */
    private function getAvailableCategories(): array
    {
        return [
            'Infraestrutura',
            'Trânsito',
            'Limpeza Urbana',
            'Segurança Pública',
            'Saúde',
            'Educação',
            'Iluminação',
            'Outro',
        ];
    }

    /**
     * Retorna os status disponíveis para filtro.
     *
     * @return array
     */
    private function getAvailableStatuses(): array
    {
        return ReportStatus::getAll();
    }
}
