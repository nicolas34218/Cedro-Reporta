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
        <p>Exibindo <strong>24 denúncias</strong> · ordenado por <strong>Mais recentes</strong></p>
    </div>

    <!-- Lista de denúncias (cards vazios por enquanto) -->
    <div class="reports-list">
        <p style="color: #999; text-align: center; padding: 40px;">
            (Cards de denúncias serão adicionados na próxima etapa)
        </p>
    </div>
</div>
@endsection