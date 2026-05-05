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
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('category', 'page'), ['category' => ''])) }}" class="sidebar-link @if(!request('category')) active @endif">● Todas</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Iluminação'])) }}" class="sidebar-link @if(request('category')=='Iluminação') active @endif">● Iluminação</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Buracos'])) }}" class="sidebar-link @if(request('category')=='Buracos') active @endif">● Buraco</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Lixo'])) }}" class="sidebar-link @if(request('category')=='Lixo') active @endif">● Lixo</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Segurança Pública'])) }}" class="sidebar-link @if(request('category')=='Segurança Pública') active @endif">● Segurança</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['category' => 'Outro'])) }}" class="sidebar-link @if(request('category')=='Outro') active @endif">● Outros</a></li>
                <div class="sidebar-section">
                    <h3>STATUS</h3>
                    <ul class="sidebar-list">
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Pendente'])) }}" class="sidebar-link @if(request('status')=='Pendente') active @endif">Pendente</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Em Andamento'])) }}" class="sidebar-link @if(request('status')=='Em Andamento') active @endif">Em Andamento</a></li>
                    <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['status' => 'Resolvido'])) }}" class="sidebar-link @if(request('status')=='Resolvido') active @endif">Resolvido</a></li>

                <div class="sidebar-section">
                    <h3>LOCALIZAÇÃO</h3>
                    <ul class="sidebar-list">
                        <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => ''])) }}" class="sidebar-link @if(!request('location')) active @endif">● Todas</a></li>
                        <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Centro'])) }}" class="sidebar-link @if(request('location')=='Centro') active @endif">● Centro</a></li>
                        <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro A'])) }}" class="sidebar-link @if(request('location')=='Bairro A') active @endif">● Bairro A</a></li>
                        <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro B'])) }}" class="sidebar-link @if(request('location')=='Bairro B') active @endif">● Bairro B</a></li>
                        <li><a href="{{ route('citizen.reports.search', array_merge(request()->except('page'), ['location' => 'Bairro C'])) }}" class="sidebar-link @if(request('location')=='Bairro C') active @endif">● Bairro C</a></li>
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