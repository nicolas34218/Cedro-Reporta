<!-- Tela Minhas Denúncias -->
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
<form 
    id="reports-filter-form" 
    action="{{ route('citizen.reports.search') }}" 
    method="get" 
    class="reports-topbar">
    <div style="display:flex; gap:12px; align-items:center;">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}" 
                class="search-box" 
                placeholder="Buscar denúncias, bairros, etc.">

            <input 
                type="hidden" 
                name="category" 
                id="filter-category" 
                value="{{ request('category') }}"
            >

            <input 
                type="hidden" 
                name="status" 
                id="filter-status" 
                value="{{ request('status') }}"
            >

            <input 
                type="hidden" 
                name="location" 
                id="filter-location" 
                value="{{ request('location') }}"
            >
        </div>

        <div class="filter-badges">
            <button 
                type="button" 
                class="badge-btn @if(!request('category')) active @endif" 
                data-value=""
            >
            Todos
            </button>

            <button 
                type="button" 
                class="badge-btn @if(request('category')=='Iluminação') active @endif" 
                data-value="Iluminação"
            >
                Iluminação
            </button>

            <button 
                type="button" 
                class="badge-btn @if(request('category')=='Buracos') active @endif" 
                data-value="Buracos"
            >
                Buraco
            </button>

            <button 
                type="button" 
                class="badge-btn @if(request('category')=='Lixo') active @endif" 
                data-value="Lixo"
            >
                Lixo
            </button>

            <button 
                type="button" 
                class="badge-btn @if(request('category')=='Segurança') active @endif" 
                data-value="Segurança">

                Segurança
            </button>

            <button 
                type="button" 
                class="badge-btn @if(request('category')=='Outros') active @endif" 
                data-value="Outros">
                
                Outros
            </button>
    </div>
</form>

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
                        {{ $reports->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reports-filter-form');
    if (!form) return;
    const catInput = document.getElementById('filter-category');
    const statusInput = document.getElementById('filter-status');

    document.querySelectorAll('.filter-badges .badge-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const value = btn.dataset.value || '';
            catInput.value = value;

            // atualizar classes ativas
            document.querySelectorAll('.filter-badges .badge-btn').forEach(b => b.classList.remove('active'));
            if (value === '') {
                const todos = document.querySelector('.filter-badges .badge-btn[data-value=""]');
                if (todos) todos.classList.add('active');
            } else {
                btn.classList.add('active');
            }

            form.submit();
        });
    });

    const searchBox = form.querySelector('input[name="q"]');
    if (searchBox) {
        searchBox.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                // comportamento padrão: o browser submete o form GET
            }
        });
    }
});
</script>

@endsection