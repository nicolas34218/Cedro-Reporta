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

                <nav class="top-nav">
                    <a href="{{ route('citizen.home') }}">Início</a>
                    <a href="{{ route('citizen.reports.index') }}">Minhas Denúncias</a>
                    <a href="#">Mapa</a>
                </nav>

                <button id="sidebar-toggle" class="hamburger" aria-label="Abrir menu" aria-expanded="false">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>

                <div class="user-area">
                    <span class="user-avatar">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <div class="reports-layout">
            <!-- Sidebar -->
            <aside class="reports-sidebar" id="reports-sidebar">
                <button id="sidebar-close" class="sidebar-close" aria-label="Fechar menu">×</button>
                <div class="sidebar-columns">
                    <div class="sidebar-left">
                        <div class="sidebar-section filter-section">
                            <button type="button" class="sidebar-toggle filter-toggle" data-target="category-panel">
                                Categorias
                            </button>

                            <div class="filter-panel" id="category-panel">
                                <form action="{{ route('citizen.reports.search') }}" method="get">
                                    <input type="hidden" name="q" value="{{ request('q') }}">
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                    <input type="hidden" name="location" value="{{ request('location') }}">

                                    <select name="category" class="sidebar-select" onchange="this.form.submit()">
                                        <option value="">Todas as categorias</option>
                                        <option value="Iluminação" @selected(request('category') === 'Iluminação')>Iluminação</option>
                                        <option value="Buracos" @selected(request('category') === 'Buracos')>Buracos</option>
                                        <option value="Lixo" @selected(request('category') === 'Lixo')>Lixo</option>
                                        <option value="Segurança Pública" @selected(request('category') === 'Segurança Pública')>Segurança</option>
                                        <option value="Outro" @selected(request('category') === 'Outro')>Outros</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="sidebar-section filter-section">
                            <button type="button" class="sidebar-toggle filter-toggle" data-target="location-panel">
                                Localização
                            </button>

                            <div class="filter-panel" id="location-panel">
                                <form action="{{ route('citizen.reports.search') }}" method="get">
                                    <input type="hidden" name="q" value="{{ request('q') }}">
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                    <input type="hidden" name="status" value="{{ request('status') }}">

                                    <select name="location" class="sidebar-select" onchange="this.form.submit()">
                                        <option value="">Todas as localizações</option>
                                        <option value="Centro" @selected(request('location') === 'Centro')>Centro</option>
                                        <option value="Bairro A" @selected(request('location') === 'Bairro A')>Bairro A</option>
                                        <option value="Bairro B" @selected(request('location') === 'Bairro B')>Bairro B</option>
                                        <option value="Bairro C" @selected(request('location') === 'Bairro C')>Bairro C</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="sidebar-section filter-section">
                            <button type="button" class="sidebar-toggle filter-toggle" data-target="status-panel">
                                Status
                            </button>

                            <div class="filter-panel" id="status-panel">
                                <form action="{{ route('citizen.reports.search') }}" method="get">
                                    <input type="hidden" name="q" value="{{ request('q') }}">
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                    <input type="hidden" name="location" value="{{ request('location') }}">

                                    <select name="status" class="sidebar-select" onchange="this.form.submit()">
                                        <option value="">Todos os status</option>
                                        <option value="Pendente" @selected(request('status') === 'Pendente')>Pendente</option>
                                        <option value="Em Andamento" @selected(request('status') === 'Em Andamento')>Em Andamento</option>
                                        <option value="Resolvido" @selected(request('status') === 'Resolvido')>Resolvido</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebar-footer">
                    <a href="{{ route('citizen.home') }}" class="sidebar-btn">← Voltar</a>
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
        const sidebarClose = document.getElementById('sidebar-close');
        const overlay = document.getElementById('sidebar-overlay');
        const layout = document.querySelector('.reports-layout');
        const sidebar = document.getElementById('reports-sidebar');

    function openSidebar() {
        layout.classList.add('sidebar-open');
        if (sidebarToggle) {
            sidebarToggle.classList.add('open');
            sidebarToggle.setAttribute('aria-expanded', 'true');
        }
    }

    function closeSidebar() {
        layout.classList.remove('sidebar-open');
        if (sidebarToggle) {
            sidebarToggle.classList.remove('open');
            sidebarToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            // Toggle sidebar visibility on all screen sizes
            layout.classList.toggle('sidebar-open');
            sidebarToggle.classList.toggle('open');
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', function (e) {
            e.stopPropagation();
            closeSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Keep sidebar state sensible on resize
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 901) {
            // ensure desktop shows sidebar by default
            layout.classList.remove('sidebar-open');
            layout.classList.remove('sidebar-closed');
        } else {
            // ensure mobile hides sidebar by default
            layout.classList.remove('sidebar-open');
            layout.classList.remove('sidebar-closed');
        }
    });

        if (sidebar) {
            sidebar.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        document.querySelectorAll('.sidebar-toggle').forEach(btn => {
            const list = btn.nextElementSibling;
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

        document.querySelectorAll('.filter-toggle').forEach(button => {
            const targetId = button.dataset.target;
            const panel = document.getElementById(targetId);

            button.addEventListener('click', () => {
                const isOpen = panel.classList.contains('open');
                document.querySelectorAll('.filter-panel').forEach(p => p.classList.remove('open'));
                if (!isOpen) {
                    panel.classList.add('open');
                }
            });
        });

        try {
            const nav = performance.getEntriesByType('navigation')[0];
            if (nav && nav.type === 'reload' && window.location.search) {
                window.location.replace(window.location.pathname);
                return;
            }
        } catch (e) {
            // ignore
        }
});
    </script>
</body>
</html>