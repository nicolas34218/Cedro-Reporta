<!-- Tela Home do Cidadão -->
@extends('layouts.citizen')

@section('title', 'Tela principal')

@section('content')
<section class="home-dashboard">
    <!-- Sidebar lateral -->
    <aside class="home-sidebar">
        <div class="sidebar-menu">
            <h3 class="sidebar-title">MENU</h3>

            <a href="{{ route('citizen.home') }}" class="sidebar-link active">
                <span class="dot">◼</span> Home
            </a>

            <a href="{{ route('citizen.reports.index') }}" class="sidebar-link">
                <span class="dot">≡</span> Minhas Denúncias
            </a>

            <a href="{{ route('citizen.reports.track-status', 1) }}" class="sidebar-link">
                <span class="dot">◉</span> Acompanhar Status
            </a>
        </div>

        <form action="{{ route('logout') }}" method="post" class="sidebar-logout">
            @csrf
            <button type="submit" class="sidebar-logout-btn">🚪 Sair</button>
        </form>
    </aside>

    <!-- Conteúdo principal -->
    <main class="home-content">
        <h1 class="home-title">Home</h1>
        <p class="home-subtitle">Bem-vindo(a) de volta</p>

        <!-- card principal -->
        <div class="hero-card">
            <div class="hero-left">
                <div class="hero-badge">
                    <img src="{{ asset('logo-cedro.png') }}" alt="Logo CedroReporta">
                </div>
                <div>
                    <h2>Faça sua cidade melhor</h2>
                    <p>Registre problemas e acompanhe as soluções</p>
                </div>
            </div>
            <a href="{{ route('citizen.reports.create') }}" class="hero-btn">+ Nova Denúncia</a>
        </div>

        <!-- cards de resumo -->
        <div class="summary-grid">
            <article class="summary-card">
                <strong>7</strong>
                <span>MINHAS DENÚNCIAS</span>
            </article>
            <article class="summary-card warning">
                <strong>3</strong>
                <span>PENDENTES</span>
            </article>
            <article class="summary-card info">
                <strong>2</strong>
                <span>EM ANDAMENTO</span>
            </article>
        </div>

        <h3 class="features-title">FUNCIONALIDADES</h3>

        <div class="feature-list">
            <a href="{{ route('citizen.reports.create') }}" class="feature-item">
                <span>📝 Registrar Denúncia</span>
                <span>›</span>
            </a>
            <a href="{{ route('citizen.reports.index') }}" class="feature-item">
                <span>📄 Visualizar Denúncia</span>
                <span>›</span>
            </a>
        </div>
    </main>
</section>
@endsection