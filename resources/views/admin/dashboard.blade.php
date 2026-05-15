@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Painel do administrador geral')

@section('content')
    <section class="statistics">
        <div class="stat-card">
            <h3>TOTAL DE DENÚNCIAS</h3>
            <p class="stat-number">{{ $statistics['total_reports'] }}</p>
            <small>{{ $statistics['total_reports'] > 0 ? 'Ativas' : 'Nenhuma' }}</small>
        </div>

        <div class="stat-card pending">
            <h3>PENDENTES</h3>
            <p class="stat-number">{{ $statistics['open_reports'] }}</p>
            <small>{{ number_format(($statistics['open_reports'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
        </div>

        <div class="stat-card in-analysis">
            <h3>EM ANDAMENTO</h3>
            <p class="stat-number">{{ $statistics['in_analysis'] }}</p>
            <small>{{ number_format(($statistics['in_analysis'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
        </div>

        <div class="stat-card resolved">
            <h3>RESOLVIDAS</h3>
            <p class="stat-number">{{ $statistics['resolved'] }}</p>
            <small>{{ number_format(($statistics['resolved'] / max($statistics['total_reports'], 1)) * 100) }}% do total</small>
        </div>
    </section>

    <section class="reports-section">
        <div class="section-header">
            <h3>Gerenciamento de Denúncias</h3>
            <div class="filters">
                <input type="text" class="search-box" placeholder="Buscar...">
                <select class="filter-select">
                    <option>Todos os status</option>
                    <option>Aberta</option>
                    <option>Em Análise</option>
                    <option>Resolvida</option>
                    <option>Fechada</option>
                </select>
                <select class="filter-select">
                    <option>Todas as categorias</option>
                    <option>Iluminação</option>
                    <option>Buracos</option>
                    <option>Lixo</option>
                    <option>Outros</option>
                </select>
            </div>
        </div>

        <table class="reports-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>TÍTULO</th>
                    <th>CATEGORIA</th>
                    <th>BAIRRO</th>
                    <th>DATA</th>
                    <th>STATUS</th>
                    <th>AÇÕES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReports as $report)
                    <tr>
                        <td>#{{ $report->id }}</td>
                        <td>{{ substr($report->title, 0, 40) }}...</td>
                        <td>
                            <span class="category-badge">{{ $report->category }}</span>
                        </td>
                        <td>{{ $report->district ?? 'Sem bairro' }}</td>
                        <td>{{ $report->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                                {{ $report->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.report.show', $report->id) }}" class="action-btn">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Nenhuma denúncia encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-info">
            Exibindo 5 de {{ $statistics['total_reports'] }} registros
        </div>
    </section>
@endsection
