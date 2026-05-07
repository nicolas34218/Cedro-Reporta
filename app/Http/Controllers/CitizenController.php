<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use Illuminate\Http\Request;

/**
 * Controlador de funcionalidades do cidadão.
 *
 * Gerencia a exibição das views da área cidadã.
 */
class CitizenController extends Controller
{
    /**
     * Exibe a página home do cidadão com estatísticas de denúncias.
     *
     * @return \Illuminate\View\View
     */
    public function home()
    {
        $user = auth()->user();

        // Total de denúncias do usuário
        $totalReports = $user->reports()->count();

        // Denúncias pendentes
        $pendingReports = $user->reports()
            ->where('status', ReportStatus::PENDING)
            ->count();

        // Denúncias em andamento (Em Análise)
        $inProgressReports = $user->reports()
            ->where('status', ReportStatus::ANALYZING)
            ->count();

        return view('citizen.home', [
            'totalReports' => $totalReports,
            'pendingReports' => $pendingReports,
            'inProgressReports' => $inProgressReports,
        ]);
    }
}
