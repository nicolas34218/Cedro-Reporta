@extends('layouts.citizen')

@section('title', 'Modo Visitante')

@section('content')

<div class="home-page-wrapper">

    <!-- Hamburger Button -->
    <button id="home-sidebar-toggle"
            class="home-hamburger"
            aria-label="Abrir menu"
            aria-expanded="false">

        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
    </button>

    <!-- Overlay -->
    <div class="home-sidebar-overlay"
        id="home-sidebar-overlay"></div>

    <section class="home-dashboard">

        <x-citizen.sidebar :visitorMode="true" />

        <main class="home-content">

            <h1 class="home-title">
                Home
            </h1>

            <x-citizen.visitor-warning />

            <p class="home-subtitle">
                Você está navegando como visitante
            </p>

            <div class="visitor-alert">

                <strong>
                    Algumas funcionalidades estão bloqueadas
                </strong>

                <p>
                    Crie uma conta para acompanhar denúncias,
                    receber notificações e verificar status da denúncia.
                </p>

            </div>

            <div class="hero-card">

                <x-citizen.hero-card :visitorMode="true" />


            </div>

        </main>

    </section>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('home-sidebar-toggle');
    const sidebar = document.getElementById('home-sidebar');
    const overlay = document.getElementById('home-sidebar-overlay');
    const dashboard = document.querySelector('.home-dashboard');

    function openSidebar() {
        dashboard.classList.add('sidebar-open');
        overlay.style.display = 'block';

        if (hamburger) {
            hamburger.classList.add('open');
            hamburger.setAttribute('aria-expanded', 'true');
        }
    }

    function closeSidebar() {
        dashboard.classList.remove('sidebar-open');
        overlay.style.display = 'none';

        if (hamburger) {
            hamburger.classList.remove('open');
            hamburger.setAttribute('aria-expanded', 'false');
        }
    }

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            if (dashboard.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
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
});
</script>
@endpush

@endsection