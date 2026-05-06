<!-- Tela Principal: Header + Sidebar -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'CedroReporta')</title>
    <link rel="stylesheet" href="{{ asset('css/citizen-reports.css') }}">
    @stack('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="reports-page-wrapper">
        <!-- Header -->
        <header class="citizen-header">
            <div class="container-header">
                <div class="brand">
                    <span class="logo">⚠</span>
                    <span class="brand-name">Cedro<span>Reporta</span></span>
                </div>
                <button id="sidebar-toggle" class="hamburger" aria-label="Abrir menu" aria-expanded="false"> 
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
                <nav class="top-nav">
                    <a href="{{ route('citizen.home') }}">Início</a>
                    <a href="{{ route('citizen.reports.index') }}">Minhas Denúncias</a>
                    <a href="#">Mapa</a>
                </nav>
                <div class="user-area">
                    <span class="user-avatar">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <div class="reports-layout">
            <!-- Sidebar -->
            <aside class="reports-sidebar" id="reports-sidebar">
                <button id="sidebar-close" class="sidebar-close" aria-label="Fechar menu">×</button>
                <div class="sidebar-columns">
                    <div class="sidebar-left">
                        <div class="sidebar-section">
                            <button type="button" class="sidebar-toggle" aria-expanded="true">CATEGORIAS</button>
                            <ul class="sidebar-list">
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('category', 'page'), ['category' => ''])) }}" class="sidebar-link @if(!request('category')) active @endif">Todas</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Iluminação'])) }}" class="sidebar-link @if(request('category')=='Iluminação') active @endif">Iluminação</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Buracos'])) }}" class="sidebar-link @if(request('category')=='Buracos') active @endif">Buraco</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Lixo'])) }}" class="sidebar-link @if(request('category')=='Lixo') active @endif">Lixo</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Segurança Pública'])) }}" class="sidebar-link @if(request('category')=='Segurança Pública') active @endif">Segurança</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Outro'])) }}" class="sidebar-link @if(request('category')=='Outro') active @endif">Outros</a></li>
                            </ul>
                        </div>
                        <div class="sidebar-section">
                            <button type="button" class="sidebar-toggle" aria-expanded="true">STATUS</button>
                            <ul class="sidebar-list">
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Pendente'])) }}" class="sidebar-link @if(request('status')=='Pendente') active @endif">Pendente</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Em Andamento'])) }}" class="sidebar-link @if(request('status')=='Em Andamento') active @endif">Em Andamento</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Resolvido'])) }}" class="sidebar-link @if(request('status')=='Resolvido') active @endif">Resolvido</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="sidebar-right">
                        <div class="sidebar-section">
                            <button type="button" class="sidebar-toggle" aria-expanded="true">LOCALIZAÇÃO</button>
                            <ul class="sidebar-list">
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => ''])) }}" class="sidebar-link @if(!request('location')) active @endif">Todas</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Centro'])) }}" class="sidebar-link @if(request('location')=='Centro') active @endif">Centro</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro A'])) }}" class="sidebar-link @if(request('location')=='Bairro A') active @endif">Bairro A</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro B'])) }}" class="sidebar-link @if(request('location')=='Bairro B') active @endif">Bairro B</a></li>
                                <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro C'])) }}" class="sidebar-link @if(request('location')=='Bairro C') active @endif">Bairro C</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="sidebar-footer">
                    <a href="{{ route('citizen.home') }}" class="sidebar-btn">← Voltar</a>
                        @unless(request()->routeIs('citizen.reports.show'))
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="sidebar-btn logout-btn">↗ Sair</button>
                        </form>
                        @endunless
                </div>
            </aside>

            <!-- Main Content -->
            <main class="reports-main">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const reportsLayout = document.querySelector('.reports-layout');
        const sidebar = document.getElementById('reports-sidebar');

        if (sidebarToggle && reportsLayout) {
            sidebarToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = reportsLayout.classList.toggle('sidebar-open');
                sidebarToggle.setAttribute('aria-expanded', String(isOpen));
            });

            // Prevent clicks inside sidebar from closing it
            if (sidebar) {
                sidebar.addEventListener('click', function (e) { e.stopPropagation(); });
            }

            // Close sidebar when clicking outside
            document.addEventListener('click', function (e) {
                if (!reportsLayout.classList.contains('sidebar-open')) return;
                reportsLayout.classList.remove('sidebar-open');
                sidebarToggle.setAttribute('aria-expanded', 'false');
            });

            // Close button inside sidebar
            const sidebarClose = document.getElementById('sidebar-close');
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function (e) {
                    e.stopPropagation();
                    reportsLayout.classList.remove('sidebar-open');
                    sidebarToggle.setAttribute('aria-expanded', 'false');
                });
            }
        }

        // Section toggles (categories / status / location)
        document.querySelectorAll('.sidebar-toggle').forEach(btn => {
            const list = btn.nextElementSibling;
            // If list contains active item, open it by default
            if (list && list.querySelector('.active')) {
                list.classList.remove('collapsed');
                btn.setAttribute('aria-expanded', 'true');
            }

            btn.addEventListener('click', () => {
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', String(!expanded));
                if (list) list.classList.toggle('collapsed');
            });
        });

        // If the user refreshed the page, clear URL filters so all reports show
        try {
            const nav = performance.getEntriesByType('navigation')[0];
            if (nav && nav.type === 'reload' && window.location.search) {
                // Reload the page without query string (clears filters)
                window.location.replace(window.location.pathname);
                return;
            }
        } catch (e) {
            // ignore (older browsers)
        }
    });
    </script>
</body>
</html>