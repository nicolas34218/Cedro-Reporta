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
 * * Gerencia o cadastro de secretárias pelo admin e o dashboard das secretárias.
 */
class SecretaryController extends Controller
{
    /**
     * Exibe o formulário de criação de secretarias.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Busca as secretarias para listar na lateral
        $secretaries = \App\Models\Secretary::orderBy('name')->get();

        return view('secretary.create', [
            'secretaries' => $secretaries,
        ]);
    }

    /**
     * Armazena uma nova secretaria no banco de dados.
     */
    public function store(\App\Http\Requests\CreateSecretaryRequest $request)
    {
        $validated = $request->validated();

        try {
            \App\Models\Secretary::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'is_active' => true,
            ]);

            return redirect()->route('secretary.create')
                ->with('success', 'Secretaria cadastrada com sucesso!');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao criar secretaria: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ocorreu um erro ao cadastrar a secretaria.');
        }
    }

    /**
     * Exibe o dashboard da secretária com denúncias da sua categoria.
     * * Mostra apenas as denúncias que pertencem à categoria associada à secretária.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request) 
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        // 1. Inicia a query base apenas com as denúncias diretas desta secretária
        $directQuery = Report::where('secretary_id', $secretary->id);

        // 2. Query das denúncias compartilhadas com esta secretária
        $sharedQuery = Report::whereHas('shares', function ($query) use ($secretary) {
            $query->where('to_secretary_id', $secretary->id);
        })->where('secretary_id', '!=', $secretary->id);

        // 3. Aplica os filtros, SE a secretária os tiver selecionado
        if ($request->filled('category')) {
            $directQuery->where('category', $request->input('category'));
            $sharedQuery->where('category', $request->input('category'));
        }
        if ($request->filled('priority')) {
            $directQuery->where('priority', $request->input('priority'));
            $sharedQuery->where('priority', $request->input('priority'));
        }
        if ($request->filled('status')) {
            $directQuery->where('status', $request->input('status'));
            $sharedQuery->where('status', $request->input('status'));
        }

        // 4. Executa as queries com a ordenação (Prioridade > ID)
        $directReports = $directQuery->with(['citizen', 'shares.fromSecretary', 'shares.toSecretary'])
            ->orderByRaw("CASE priority WHEN 'Alta' THEN 1 WHEN 'Média' THEN 2 WHEN 'Baixa' THEN 3 ELSE 4 END")
            ->orderBy('id', 'ASC')
            ->get();

        $sharedReports = $sharedQuery->with(['citizen', 'secretary', 'shares.fromSecretary', 'shares.toSecretary'])
            ->orderByRaw("CASE priority WHEN 'Alta' THEN 1 WHEN 'Média' THEN 2 WHEN 'Baixa' THEN 3 ELSE 4 END")
            ->orderBy('id', 'ASC')
            ->get();

        // 5. Recalcula as estatísticas explicitamente usando o ID da secretária
        $statistics = [
            'total_reports' => \App\Models\Report::where('secretary_id', $secretary->id)->count(),
            'shared_reports' => \App\Models\Report::whereHas('shares', function ($query) use ($secretary) {
                $query->where('to_secretary_id', $secretary->id);
            })->where('secretary_id', '!=', $secretary->id)->count(),
            'pending_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Pendente')
                ->count(),
            'analyzing_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Em Análise')
                ->count(),
            'resolved_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Resolvida')
                ->count(),
        ];

        // 5. Busca as categorias do banco de dados para os botões de filtro
        $categories = \App\Models\Category::orderBy('name', 'asc')->get();

        return view('secretary.dashboard', [
            'directReports' => $directReports,
            'sharedReports' => $sharedReports,
            'statistics' => $statistics,
            'category' => $secretary->name,
            'categories' => $categories, 
        ]);
    }

    /**
     * Exibe a tela de classificação de prioridades das denúncias da secretária.
     * * Mostra apenas as denúncias atribuídas à secretária autenticada.
     *
     * @return \Illuminate\View\View
     */
    public function classifyReports()
    {
        /** @var Secretary $secretary */
        $secretary = Auth::user();

        // Filtra denúncias da secretária logada que ainda não têm prioridade OU que você deseja classificar
        $reports = \App\Models\Report::where('secretary_id', $secretary->id)
            ->whereNull('priority') 
            ->latest()
            ->get();

        // Recalcula as estatísticas especificamente para o que aparece nesta tela
        $statistics = [
            'total_reports' => \App\Models\Report::where('secretary_id', $secretary->id)->count(),
            'pending_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Pendente')->count(), 
            'analyzing_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Em Análise')->count(),
            'resolved_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
                ->where('status', 'Resolvida')->count(),
        ];

        return view('secretary.classify_reports', [
            'reports' => $reports,
            'statistics' => $statistics,
        ]);
    }
}