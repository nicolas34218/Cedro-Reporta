<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Controlador do painel administrativo.
 * Gerencia funcionalidades do painel administrativo.
 */
class AdminController extends Controller
{
    /**
     * Exibe o painel administrativo com resumo das denúncias.
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
     * @param Report $report
     * @return \Illuminate\View\View
     */
    public function showReport(Report $report)
    {
        $report->load('user');

        return view('admin.report-detail', [
            'report' => $report,
        ]);
    }

    /**
     * Atualiza o status de uma denúncia.
     *
     * @param Report $report
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateReportStatus(Report $report, Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(ReportStatus::getAll())],
        ]);

        if ($report->status === $validated['status']) {
            return redirect()->route('admin.report.show', $report)
                ->with('info', 'O status da denúncia já está definido como ' . $report->status . '.');
        }

        $report->status = $validated['status'];
        $report->save();

        return redirect()->route('admin.report.show', $report)
            ->with('success', 'Status da denúncia atualizado com sucesso!');
    }
}
