@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard Secretária')
@section('page-title', 'Dashboard da Secretária')
@section('page-subtitle', 'Visualização das denúncias da sua categoria')

@section('content')
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

    <section class="reports-section">
        <div class="section-header">
            <h3>Denúncias da categoria: {{ $category }}</h3>
        </div>

        @if ($reports->isEmpty())
            <p>Nenhuma denúncia cadastrada para esta categoria.</p>
        @else
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Denunciante</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Localização</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->title }}</td>
                            <td>{{ Str::limit($report->description, 50) }}</td>
                            <td>{{ $report->citizen->name ?? 'Desconhecido' }}</td>
                            <td>{{ $report->category }}</td>
                            <td>{{ $report->status }}</td>
                            <td>{{ $report->location ?? 'Não informado' }}</td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection

