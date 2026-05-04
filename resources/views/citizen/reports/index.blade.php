@extends('layouts.citizen-reports')

@section('title', 'Visualizar Denúncias')

@section('content')
<div class="reports-content">
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: #e8f7ee; color: #14532d; border: 1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: #fdecec; color: #991b1b; border: 1px solid #fca5a5;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Topbar com busca -->
    <div class="reports-topbar">
        <input type="text" class="search-box" placeholder="Buscar denúncias, bairros, etc.">
        <div class="filter-badges">
            <button class="badge-btn active">Todos</button>
            <button class="badge-btn">Iluminação</button>
            <button class="badge-btn">Buraco</button>
            <button class="badge-btn">Lixo</button>
            <button class="badge-btn">Calçada</button>
        </div>
    </div>

    <!-- Contagem de resultados -->
    <div class="results-info">
        <p>
            Exibindo
            <strong>{{ $reports->total() }}</strong>
            denúncia{{ $reports->total() === 1 ? '' : 's' }} · ordenado por
            <strong>Mais recentes</strong>
        </p>
    </div>

    <!-- Lista de denúncias -->
    <div class="reports-list">
        @forelse ($reports as $report)
            <article class="report-card">
                <div class="report-card-header">
                    <div>
                        <h3 class="report-title">{{ $report->title }}</h3>
                        <p class="report-location">
                            {{ $report->location ?: 'Localização não informada' }}
                        </p>
                    </div>

                    <span class="report-status status-{{ \Illuminate\Support\Str::slug($report->status) }}">
                        {{ $report->status }}
                    </span>
                </div>

                <p class="report-description">
                    {{ \Illuminate\Support\Str::limit($report->description, 180) }}
                </p>

                <div class="report-meta">
                    <span>{{ $report->category }}</span>
                    <span>{{ $report->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="report-actions">
                    <a href="{{ route('citizen.reports.show', $report->id) }}">Ver detalhes</a>
                    <a href="{{ route('citizen.reports.track-status', $report->id) }}">Acompanhar status</a>
                </div>
            </article>
        @empty
            <div class="reports-empty">
                <p>Você ainda não registrou nenhuma denúncia.</p>
                <a href="{{ route('citizen.reports.create') }}">Criar primeira denúncia</a>
            </div>
        @endforelse
    </div>

    @if ($reports->hasPages())
        <div class="reports-pagination">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection