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

class ReportController extends Controller
{
    use FormatReport;

    public function create()
    {
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        $captchaQuestion = $this->generateCaptchaQuestion();

        return view('citizen.reports.create', [
            'categories' => $categories,
            'captchaQuestion' => $captchaQuestion,
            'formAction' => route('citizen.reports.store'),
            'visitorMode' => false,
        ]);
    }

    public function createVisitor()
    {
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();
        $captchaQuestion = $this->generateCaptchaQuestion();

        return view('citizen.reports.create', [
            'categories' => $categories,
            'captchaQuestion' => $captchaQuestion,
            'formAction' => route('visitor.reports.store'),
            'visitorMode' => true,
        ]);
    }

    public function store(ReportRequest $request)
    {
        return $this->handleReportSubmission($request, 'citizen.reports.index', false);
    }

    public function storeVisitor(ReportRequest $request)
    {
        return $this->handleReportSubmission($request, 'visitor.reports.create', true);
    }

    private function handleReportSubmission(ReportRequest $request, string $redirectRoute, bool $visitorMode)
    {
        try {
            if (!$this->validateCaptchaAnswer($request)) {
                return back()
                    ->withInput()
                    ->withErrors(['captcha_answer' => 'A resposta do Captcha está incorreta.'])
                    ->with('error', 'Por favor, confirme que você não é um robô.');
            }

            $validated = $request->validated();

            // 1. Localização
            $location = null;
            if (!empty($validated['address_reference']) || !empty($validated['district'])) {
                $parts = array_filter([
                    $validated['address_reference'] ?? '',
                    $validated['district'] ?? ''
                ]);
                $location = implode(' - ', $parts);
            }

            // 2. Upload de Imagem
            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file = $request->file('image');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->makeDirectory('reports');
                $imagePath = Storage::disk('local')->putFileAs('reports', $file, $filename, 'private');
            }

            // 3. Validação de Categoria
            $category = \App\Models\Category::where('name', $validated['category'])->first();
            if (!$category) {
                return back()->withInput()->with('error', 'Categoria inválida.');
            }

            $secretaryId = $category->secretary_id;
            $secretary = $secretaryId ? \App\Models\Secretary::find($secretaryId) : null;

            // 4. Criação da Denúncia
            $report = Report::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'status' => ReportStatus::PENDING,
                'location' => $location,
                'image_path' => $imagePath,
                'secretary_id' => $secretaryId,
            ]);

            // 5. Notificações da secretaria
            if ($secretary) {
                try {
                    $secretary->notify(new \App\Notifications\NewReportAssigned($report));
                } catch (\Exception $notifyError) {
                    Log::error('Erro ao notificar secretária: ' . $notifyError->getMessage());
                }
            }

            if ($visitorMode) {
                return redirect()
                    ->route('visitor.reports.create')
                    ->with('success', 'Denúncia registrada com sucesso! ID: #' . $report->id)
                    ->with('visitor_notice', 'Sua denúncia foi registrada como visitante. Você não poderá receber notificações automáticas nem acompanhar o status pelo sistema.');
            }

            return redirect()
                ->route($redirectRoute)
                ->with('success', 'Denúncia registrada com sucesso! ID: #' . $report->id);

        } catch (\Exception $e) {
            Log::error('Erro fatal ao criar denúncia: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao registrar a denúncia.');
        }
    }

    private function validateCaptchaAnswer(ReportRequest $request): bool
    {
        $expected = session('report_captcha_answer');
        $answer = $request->input('captcha_answer');

        session()->forget('report_captcha_answer');

        return $expected !== null && is_numeric($answer) && intval($answer) === intval($expected);
    }

    private function generateCaptchaQuestion(): string
    {
        $left = random_int(1, 9);
        $right = random_int(1, 9);
        $operators = ['+', '-'];
        $operator = $operators[random_int(0, count($operators) - 1)];

        if ($operator === '-' && $left < $right) {
            [$left, $right] = [$right, $left];
        }

        $answer = $operator === '+' ? $left + $right : $left - $right;
        session(['report_captcha_answer' => $answer]);

        return "Quanto é {$left} {$operator} {$right}?";
    }

public function index()
    {
        $reports = auth()->user()->reports()->orderByDesc('created_at')->paginate(10);
        
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();

        return view('citizen.reports.index', [
            'reports' => $reports,
            'categories' => $categories,
            'statuses' => ReportStatus::getAll(),
        ]);
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        $report->load('citizen');
        $reportFormatted = $this->formatReport($report, includeDescription: true);

        return view('citizen.reports.show', [
            'report' => $report,
            'reportFormatted' => $reportFormatted,
        ]);
    }

    public function trackStatus(Report $report)
    {
        $this->authorize('track', $report);
        return view('citizen.reports.track-status', ['report' => $report]);
    }

public function search(Request $request)
    {
        $query = auth()->user()->reports();

        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('location', 'like', "%{$term}%");
            });
        }
        
        $filters = [
            'category' => $request->input('category'),
            'location' => $request->input('location'),
            'status' => $request->input('status'),
        ];
        
        $query->filter($filters);
        $reports = $query->orderByDesc('created_at')->paginate(10);

        $categories = \App\Models\Category::orderBy('name', 'asc')->get();

        return view('citizen.reports.index', [
            'reports' => $reports,
            'filters' => $filters,
            'categories' => $categories, 
            'statuses' => ReportStatus::getAll(),
        ]);
    }

    public function getImage(Report $report)
    {
        $this->authorize('view', $report);
        if (!$report->image_path || !Storage::disk('local')->exists($report->image_path)) {
            abort(404);
        }
        $path = Storage::disk('local')->path($report->image_path);
        return response()->file($path);
    }
}