<!-- Tela Home do Cidadão -->
@extends('layouts.citizen')

@section('title', 'Tela principal')

@section('content')
<div class="home-page-wrapper">
    <!-- Hamburger Button -->
    <button id="home-sidebar-toggle" class="home-hamburger" aria-label="Abrir menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>

    <!-- Overlay -->
    <div class="home-sidebar-overlay" id="home-sidebar-overlay"></div>

    <section class="home-dashboard">
        <!-- Sidebar lateral -->
        <aside class="home-sidebar" id="home-sidebar">
            <button id="home-sidebar-close" class="home-sidebar-close" aria-label="Fechar menu">×</button>
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
            <a href="{{ route('citizen.reports.create') }}" class="hero-btn">+ Novo Report</a>
        </div>

        <!-- cards de resumo -->
        <div class="summary-grid">
            <article class="summary-card">
                <strong>{{ $totalReports }}</strong>
                <span>MEUS REGISTROS</span>
            </article>
            <article class="summary-card warning">
                <strong>{{ $pendingReports }}</strong>
                <span>PENDENTES</span>
            </article>
            <article class="summary-card info">
                <strong>{{ $inProgressReports }}</strong>
                <span>EM ANDAMENTO</span>
            </article>
        </div>

        <h3 class="features-title">FUNCIONALIDADES</h3>

        <div class="feature-list">
            <a href="{{ route('citizen.reports.create') }}" class="feature-item">
                <span>📝 Registrar Report</span>
                <span>›</span>
            </a>
            <a href="{{ route('citizen.reports.index') }}" class="feature-item">
                <span>📄 Visualizar Report</span>
                <span>›</span>
            </a>
        </div>
    </main>
    </section>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.getElementById('home-sidebar-toggle');
        const sidebar = document.getElementById('home-sidebar');
        const overlay = document.getElementById('home-sidebar-overlay');
        const closeBtn = document.getElementById('home-sidebar-close');
        const dashboard = document.querySelector('.home-dashboard');

        function openSidebar() {
            // add both states; CSS will choose docked vs overlay based on media queries
            dashboard.classList.add('sidebar-open');
            dashboard.classList.add('sidebar-docked');
            if (hamburger) {
                hamburger.classList.add('open');
                hamburger.setAttribute('aria-expanded', 'true');
            }
        }

        function closeSidebar() {
            dashboard.classList.remove('sidebar-open');
            dashboard.classList.remove('sidebar-docked');
            if (hamburger) {
                hamburger.classList.remove('open');
                hamburger.setAttribute('aria-expanded', 'false');
            }
        }

        if (hamburger) {
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                // Toggle both; CSS controls visual mode via media queries
                dashboard.classList.toggle('sidebar-open');
                dashboard.classList.toggle('sidebar-docked');
                hamburger.classList.toggle('open');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebar();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        if (sidebar) {
            sidebar.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Reset sidebar on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 901) {
                dashboard.classList.remove('sidebar-open');
            }
        });
    });
</script>