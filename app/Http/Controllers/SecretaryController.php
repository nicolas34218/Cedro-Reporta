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
/**
     * Exibe o formulário de criação de secretarias.
     */
    public function create()
    {
        // Busca as secretarias para listar na lateral
        $secretaries = \App\Models\Secretary::orderBy('name')->get();

        return view('secretary.create', [
            'secretaries' => $secretaries,
            // Não enviamos mais $categories para a view
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
                'admin_id' => auth()->id(), // Mantém o registro de quem criou
                // 'category' foi removido daqui!
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
     * 
     * Mostra apenas as denúncias que pertencem à categoria associada à secretária.
     *
     * @return \Illuminate\View\View
     */
public function dashboard()
{
    /** @var Secretary $secretary */
    $secretary = Auth::user();

    // Obtém todas as denúncias onde o secretary_id é igual ao ID da secretária logada
    $reports = Report::where('secretary_id', $secretary->id)
        ->with('citizen')
        ->latest()
        ->get();

    // Recalcula as estatísticas explicitamente usando o ID da secretária
    $statistics = [
        'total_reports' => Report::where('secretary_id', $secretary->id)->count(),
        'pending_reports' => Report::where('secretary_id', $secretary->id)
            ->where('status', 'Pendente') // Verifique se este é o status exato no seu Enum/Banco
            ->count(),
        'analyzing_reports' => Report::where('secretary_id', $secretary->id)
            ->where('status', 'Em Análise')
            ->count(),
        'resolved_reports' => Report::where('secretary_id', $secretary->id)
            ->where('status', 'Resolvida')
            ->count(),
    ];

    return view('secretary.dashboard', [
        'reports' => $reports,
        'statistics' => $statistics,
        'category' => $secretary->name, // Ajustado para mostrar o nome da secretaria, já que categoria não existe mais
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

    // Filtra denúncias da secretária logada que ainda não têm prioridade OU que você deseja classificar
    $reports = \App\Models\Report::where('secretary_id', $secretary->id)
        ->whereIn('status', ['Aberta', 'Em Análise']) // Ajuste conforme os status que você permite classificar
        ->latest()
        ->get();

    // Recalcula as estatísticas especificamente para o que aparece nesta tela

    $statistics = [
        'total_reports' => \App\Models\Report::where('secretary_id', $secretary->id)->count(),
        'pending_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
            ->where('status', 'Pendente')->count(), // Alterado de 'Aberta' para 'Pendente'
        'analyzing_reports' => \App\Models\Report::where('secretary_id', $secretary->id)
            ->where('status', 'Em Análise')->count(),
    ];

        return view('secretary.classify_reports', [
            'reports' => $reports,
            'statistics' => $statistics,
        ]);
    }
}
