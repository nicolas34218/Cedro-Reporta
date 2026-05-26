<?php

namespace App\Http\Controllers;

use App\Constants\ReportConstants;
use App\Enums\ReportStatus;
use App\Http\Requests\ReportRequest;
use App\Models\Report;
use App\Models\Citizen;
use App\Models\Secretary;
use App\Traits\FormatReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controlador de denúncias/relatórios.
 *
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
     * * @param ReportRequest $request
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

            // Processa o upload de imagem se fornecida
            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                try {
                    $file = $request->file('image');
                    
                    // Gera nome único para o arquivo
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    
                    // Cria a pasta se não existir
                    Storage::disk('local')->makeDirectory('reports');
                    
                    // Armazena a imagem no storage (pasta: reports)
                    $imagePath = Storage::disk('local')->putFileAs(
                        'reports',
                        $file,
                        $filename,
                        'private'
                    );
                    
                    Log::info('Imagem enviada com sucesso', [
                        'filename' => $filename,
                        'path' => $imagePath,
                        'size' => $file->getSize(),
                    ]);
                } catch (\Exception $imageException) {
                    Log::error('Erro ao processar upload de imagem', [
                        'user_id' => auth()->id(),
                        'error' => $imageException->getMessage(),
                        'file' => $request->file('image')->getClientOriginalName() ?? 'unknown',
                    ]);
                    // Continua sem imagem ao invés de falhar completamente
                    $imagePath = null;
                }
            }

            // 1. Busca primeiro a secretaria responsável ANTES de criar a denúncia
            $secretaryId = null;
            $secretariesToNotify = collect();

            $category = \App\Models\Category::where('name', $validated['category'])->first();

            if ($category) {
                // Pega todas as ativas
                $secretariesToNotify = $category->secretaries()->where('is_active', true)->get();
                
                if ($secretariesToNotify->isNotEmpty()) {
                    // Guarda o ID da primeira para vincular à denúncia
                    $secretaryId = $secretariesToNotify->first()->id;
                } else {
                    Log::warning('Nenhuma secretária ativa encontrada para a categoria', [
                        'category' => $validated['category'],
                    ]);
                }
            } else {
                Log::warning('Categoria não encontrada no banco de dados', [
                    'category_name' => $validated['category'],
                ]);
            }

            // 2. Cria a denúncia com o cidadão autenticado E a secretaria
            $report = auth()->user()->reports()->create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'status' => ReportStatus::PENDING, 
                'location' => $location,
                'image_path' => $imagePath, 
                'secretary_id' => $secretaryId, 
            ]);

            // 3. Dispara as notificações se houverem secretárias
            if ($secretariesToNotify->isNotEmpty()) {
                foreach ($secretariesToNotify as $secretary) {
                    $secretary->notify(new \App\Notifications\NewReportAssigned($report));
                }

                Log::info('Denúncia atribuída automaticamente', [
                    'report_id' => $report->id,
                    'primary_secretary_id' => $secretaryId,
                    'total_secretaries_notified' => $secretariesToNotify->count(),
                    'category' => $validated['category'],
                ]);
            }

            // Log de sucesso
            Log::info('Denúncia criada com sucesso', [
                'report_id' => $report->id,
                'user_id' => auth()->id(),
                'category' => $report->category,
                'has_image' => !is_null($imagePath),
            ]);

            return redirect()
                ->route('citizen.reports.index')
                ->with('success', 'Denúncia registrada com sucesso! ID: #' . $report->id);
        } catch (\Exception $e) {
            // Log de erro detalhado (sem expor informações sensíveis ao usuário)
            Log::error('Erro ao criar denúncia', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Retorna com mensagem de erro apropriada
            $errorMessage = 'Ocorreu um erro ao registrar a denúncia. Tente novamente.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorMessage = 'Email já cadastrado no sistema.';
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Lista todas as denúncias do usuário autenticado.
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
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function show(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('view', $report);

        // Carrega o cidadão relacionado
        $report->load('citizen');

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
     * Exibe página de busca e filtro de denúncias.
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
            'categories' => ReportConstants::getCategories(),
            'statuses' => ReportStatus::getAll(),
        ]);
    }

    /**
     * Serve a imagem privada da denúncia.
     *
     * @param Report $report
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getImage(Report $report)
    {
        // Autoriza o acesso usando a Policy
        $this->authorize('view', $report);

        // Verifica se a imagem existe
        if (!$report->image_path || !Storage::disk('local')->exists($report->image_path)) {
            abort(404);
        }

        // Retorna o arquivo como resposta inline (exibe na página)
        $path = Storage::disk('local')->path($report->image_path);
        $mimeType = mime_content_type($path) ?: 'image/jpeg';

        return response()->file($path, [
            'Content-Type' => $mimeType,
        ]);
    }
}