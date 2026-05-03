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
                <div class="user-area">
                    <span class="user-avatar">{{ substr(auth()->user()->name, 0, 2) }}</span>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <div class="reports-layout">
            <!-- Sidebar -->
            <aside class="reports-sidebar">
                <div class="sidebar-section">
                    <h3>CATEGORIAS</h3>
                    <ul class="sidebar-list">
                        <li><a href="#" class="sidebar-link active">● Todas</a></li>
                        <li><a href="#" class="sidebar-link">● Iluminação</a></li>
                        <li><a href="#" class="sidebar-link">● Buraco</a></li>
                        <li><a href="#" class="sidebar-link">● Lixo</a></li>
                        <li><a href="#" class="sidebar-link">● Calçada</a></li>
                        <li><a href="#" class="sidebar-link">● Outros</a></li>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3>STATUS</h3>
                    <ul class="sidebar-list">
                        <li><a href="#" class="sidebar-link">Pendente</a></li>
                        <li><a href="#" class="sidebar-link">Em Andamento</a></li>
                        <li><a href="#" class="sidebar-link">Resolvido</a></li>
                    </ul>
                </div>

                <div class="sidebar-footer">
                    <a href="{{ route('citizen.home') }}" class="sidebar-btn">← Voltar</a>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="sidebar-btn logout-btn">↗ Sair</button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="reports-main">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>