<!-- Tela Home do Cidadão -->
@extends('layouts.citizen')

@section('title', 'Tela principal')


@section('content')

@extends('layouts.citizen')

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
        <!-- Sidebar lateral Reutilizável -->
        <x-citizen.sidebar />

    <!-- Conteúdo principal -->
    <main class="home-content">
        <h1 class="home-title">Home</h1>
        <p class="home-subtitle">Bem-vindo(a) de volta</p>

        <!-- card principal -->
       <x-citizen.hero-card :visitorMode="false" />

        <!-- cards de resumo -->
        <div class="summary-grid">
            <x-citizen.summary-card
                :value="$totalReports"
                label="MEUS REGISTROS" />

            <x-citizen.summary-card
                :value="$pendingReports"
                label="PENDENTES"
                type="warning" />

            <x-citizen.summary-card
                :value="$inProgressReports"
                label="EM ANDAMENTO"
                type="info" />
        </div>

        <h3 class="features-title">FUNCIONALIDADES</h3>

        <div class="feature-list">
            <a
                href="{{ ($visitorMode ?? false)
                ? route('visitor.reports.create')
                : route('citizen.reports.create') }}"
                class="hero-btn">

                Nova Denúncia

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