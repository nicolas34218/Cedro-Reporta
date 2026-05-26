@extends('layouts.admin', ['active' => 'dashboard'])

@push('styles')
<link rel='stylesheet' href='/css/admin/reports.css'>
<style>
    /* Estilos extras para garantir que os cards fiquem perfeitos no Admin */
    .report-category { font-size: 0.875rem; color: #4f46e5; font-weight: 600; margin-right: 15px; }
    .report-citizen { font-size: 0.875rem; color: #6b7280; margin-right: 15px; }
    .report-card-action a:hover { background-color: #2563eb; }
</style>
@endpush

@section('content')
    <div class="classify-header">
        <h1 class="page-title">Painel de Controle</h1>
        <p class="page-subtitle">Visão geral das denúncias e estatísticas de todos os setores do sistema.</p>
    </div>

    <section class="statistics-section">
        <div class="stat-card">
            <p class="stat-label">TOTAL DE DENÚNCIAS</p>
            <h2 class="stat-number">{{ $statistics['total_reports'] ?? 0 }}</h2>
            <div class="stat-underline" style="background-color: #3b82f6;"></div>
        </div>

        <div class="stat-card pending">
            <p class="stat-label">ABERTAS</p>
            <h2 class="stat-number">{{ $statistics['open_reports'] ?? 0 }}</h2>
            <div class="stat-underline" style="background-color: #ef4444;"></div>
        </div>

        <div class="stat-card in-progress">
            <p class="stat-label">EM ANÁLISE</p>
            <h2 class="stat-number">{{ $statistics['in_analysis'] ?? 0 }}</h2>
            <div class="stat-underline" style="background-color: #f59e0b;"></div>
        </div>

        <div class="stat-card resolved">
            <p class="stat-label">RESOLVIDAS</p>
            <h2 class="stat-number">{{ $statistics['resolved'] ?? 0 }}</h2>
            <div class="stat-underline" style="background-color: #10b981;"></div>
        </div>
    </section>

    <section class="reports-list-section">
        <h3 class="section-title" style="margin-top: 20px;">DENÚNCIAS MAIS RECENTES</h3>

        @if($recentReports->isEmpty())
            <div class="no-reports-message">
                <i class="fas fa-inbox"></i>
                <p>Nenhuma denúncia registrada no sistema ainda.</p>
            </div>
        @else
        <div class="reports-cards-container">
            @foreach($recentReports as $report)
                <div class="report-card">
                    <div class="report-card-content">
                        <h4 class="report-title">#{{ $report->id }} - {{ $report->title }}</h4>
                        
                        <div class="report-meta" style="margin-top: 8px; margin-bottom: 12px;">
                            <span class="report-category">
                                <i class="fas fa-tags"></i> {{ $report->category }}
                            </span>
                            <span class="report-location">
                                <i class="fas fa-map-pin"></i> {{ $report->location ?? 'Sem localização' }}
                            </span>
                            <span class="report-date">
                                <i class="far fa-calendar-alt"></i> {{ $report->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span class="report-citizen">
                                <i class="fas fa-user"></i> {{ $report->citizen->name ?? 'Anônimo' }}
                            </span>
                        </div>

                        <span class="status-badge status-{{ strtolower(str_replace([' ', 'á'], ['-', 'a'], $report->status)) }}" style="display: inline-block;">
                            {{ $report->status }}
                        </span>
                    </div>

                    <div class="report-card-action" style="display: flex; align-items: center; justify-content: flex-end;">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </section>
@endsection