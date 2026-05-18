@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard Secretária')
@section('page-title', 'Dashboard da Secretária')
@section('page-subtitle', 'Visualização das denúncias da sua categoria')

@push('styles')
<link rel="stylesheet" href="/css/secretary/dashboard.css">
@endpush

@section('content')
    <!-- Cards de Estatísticas -->
    <section class="statistics">
        <div class="stat-card">
            <h3>TOTAL DE DENÚNCIAS</h3>
            <p class="stat-number">{{ $statistics['total_reports'] }}</p>
        </div>

        <div class="stat-card pending">
            <h3>PENDENTES</h3>
            <p class="stat-number">{{ $statistics['pending_reports'] }}</p>
        </div>

        <div class="stat-card in-analysis">
            <h3>EM ANÁLISE</h3>
            <p class="stat-number">{{ $statistics['analyzing_reports'] }}</p>
        </div>

        <div class="stat-card resolved">
            <h3>RESOLVIDAS</h3>
            <p class="stat-number">{{ $statistics['resolved_reports'] }}</p>
        </div>
    </section>

    <!-- Tabela de Denúncias -->
    <section class="reports-section">
        <div class="section-header">
            <h3>Denúncias da categoria: <strong>{{ $category }}</strong></h3>
        </div>

        @if ($reports->isEmpty())
            <div class="no-reports-message">
                <i class="fas fa-inbox"></i>
                <p>Nenhuma denúncia cadastrada para esta categoria.</p>
            </div>
        @else
            <div class="reports-table-wrapper">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Denunciante</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td class="id-cell">#{{ $report->id }}</td>
                                <td class="title-cell">{{ Str::limit($report->title, 45) }}</td>
                                <td class="citizen-cell">
                                    @if($report->citizen)
                                        <span title="{{ $report->citizen->email }}">{{ Str::limit($report->citizen->name, 20) }}</span>
                                    @else
                                        <span class="text-muted">Desconhecido</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($report->priority)
                                        <span class="priority-badge priority-{{ strtolower($report->priority) }}">
                                            {{ $report->priority }}
                                        </span>
                                    @else
                                        <span class="priority-badge priority-unclassified">
                                            Não classificada
                                        </span>
                                    @endif
                                </td>
                                <td class="date-cell">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                <td class="actions-cell">
                                    <a href="{{ route('admin.report.show', $report) }}" class="btn-action btn-view" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('priority.edit', $report) }}" class="btn-action btn-priority" title="Classificar Prioridade">
                                        <i class="fas fa-flag"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

