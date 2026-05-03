@extends('layouts.citizen-reports')

@section('title', 'Visualizar Denúncias')

@section('content')
<div class="reports-content">
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