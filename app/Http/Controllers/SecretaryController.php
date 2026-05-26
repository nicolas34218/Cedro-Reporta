<?php

namespace App\Http\Controllers;

use App\Constants\ReportConstants;
use App\Http\Requests\CreateSecretaryRequest;
use App\Models\Report;
use App\Models\Secretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de funcionalidades da secretária.
 * 
 * Gerencia o cadastro de secretárias pelo admin e o dashboard das secretárias.
 */
class SecretaryController extends Controller
{
    /**
     * Exibe o formulário para criar nova secretária.
     * Apenas admins podem acessar.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Obtém todas as categorias disponíveis
        $categories = ReportConstants::getCategories();
        $secretaries = Secretary::latest()->get();

        return view('secretary.create', [
            'categories' => $categories,
            'secretaries' => $secretaries,
        ]);
    }

    /**
     * Armazena uma nova secretária no banco de dados.
     * Apenas admins podem criar secretárias.
     *
     * @param CreateSecretaryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateSecretaryRequest $request)
    {
        try {
            // Cria nova secretária com valores validados
            $secretary = Secretary::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'category' => $request->validated('category'),
                'is_active' => true,
                'admin_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', "Secretária '{$secretary->name}' da categoria '{$secretary->category}' criada com sucesso!");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao criar a secretária. Tente novamente.');
        }
    }

    /**
     * Exibe o dashboard da secretária com denúncias da sua categoria.
     * 
     * Mostra apenas as denúncias que pertencem à categoria associada à secretária.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        // Valida se o usuário é uma secretária
        if (!($secretary instanceof Secretary)) {
            abort(403, 'Acesso não autorizado.');
        }

        // Obtém todas as denúncias atribuídas a esta secretária
        $reports = Report::where('secretary_id', $secretary->id)
            ->with('citizen')
            ->latest()
            ->get();

        // Calcula estatísticas das denúncias atribuídas
        $statistics = [
            'total_reports' => $secretary->assignedReports()->count(),
            'pending_reports' => $secretary->assignedReports()
                ->where('status', 'Pendente')
                ->count(),
            'analyzing_reports' => $secretary->assignedReports()
                ->where('status', 'Em Análise')
                ->count(),
            'resolved_reports' => $secretary->assignedReports()
                ->where('status', 'Resolvida')
                ->count(),
        ];

        return view('secretary.dashboard', [
            'reports' => $reports,
            'statistics' => $statistics,
            'category' => $secretary->category,
        ]);
    }

    /**
     * Exibe a tela de classificação de prioridades das denúncias da secretária.
     * 
     * Mostra apenas as denúncias atribuídas à secretária autenticada.
     *
     * @return \Illuminate\View\View
     */
    public function classifyReports()
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        // Valida se o usuário é uma secretária
        if (!($secretary instanceof Secretary)) {
            abort(403, 'Acesso não autorizado.');
        }

        // Obtém todas as denúncias atribuídas a esta secretária
        $reports = Report::where('secretary_id', $secretary->id)
            ->with('citizen')
            ->latest()
            ->paginate(10);

        // Calcula estatísticas das denúncias atribuídas
        $statistics = [
            'total_reports' => $secretary->assignedReports()->count(),
            'pending_reports' => $secretary->assignedReports()
                ->where('status', 'Pendente')
                ->count(),
            'analyzing_reports' => $secretary->assignedReports()
                ->where('status', 'Em Análise')
                ->count(),
        ];

        return view('secretary.reports', [
            'reports' => $reports,
            'statistics' => $statistics,
        ]);
    }
}
