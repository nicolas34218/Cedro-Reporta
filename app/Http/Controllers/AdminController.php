<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador do painel administrativo.
 *
 * Gerencia funcionalidades do painel administrativo.
 */
class AdminController extends Controller
{
    /**
     * Exibe o painel administrativo com resumo das denúncias.
     *
     * Subtasks validadas:
     * - O painel deve exibir resumo das denúncias.
     * - Apenas Admin e Secretário podem acessar o painel.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Coleta estatísticas de denúncias
        $statistics = [
            'total_reports' => Report::count(),
            'open_reports' => Report::where('status', 'Aberta')->count(),
            'in_analysis' => Report::where('status', 'Em Análise')->count(),
            'resolved' => Report::where('status', 'Resolvida')->count(),
            'closed' => Report::where('status', 'Fechada')->count(),
        ];

        // Obtém denúncias recentes (últimas 10)
        $recentReports = Report::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Agrupa denúncias por categoria
        $reportsByCategory = Report::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Agrupa denúncias por status
        $reportsByStatus = Report::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('admin.dashboard', [
            'statistics' => $statistics,
            'recentReports' => $recentReports,
            'reportsByCategory' => $reportsByCategory,
            'reportsByStatus' => $reportsByStatus,
        ]);
    }

    /**
     * Exibe a lista completa de denúncias com filtros.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function listReports(Request $request)
    {
        $query = Report::with('user');

        // Aplica filtros se fornecidos
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->latest()->paginate(15);

        return view('admin.reports', [
            'reports' => $reports,
        ]);
    }

    /**
     * Exibe os detalhes de uma denúncia específica.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function showReport($id)
    {
        $report = Report::with('user')->findOrFail($id);

        return view('admin.report-detail', [
            'report' => $report,
        ]);
    }

    /**
     * Atualiza o status de uma denúncia.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateReportStatus($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:Aberta,Em Análise,Resolvida,Fechada',
        ]);

        $report = Report::findOrFail($id);
        $report->update($validated);

        return back()->with('success', 'Status da denúncia atualizado com sucesso!');
    }
}
