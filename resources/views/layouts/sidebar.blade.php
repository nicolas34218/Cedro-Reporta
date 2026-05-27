@props(['active' => 'dashboard'])

<aside class="admin-sidebar">
    <div class="logo-section">
        <div class="logo">⚠</div>
        <h1>Cedro<span>Reporta</span></h1>
        <p class="user-type">Administrador Geral</p>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}"
            class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.reports') }}"
            class="nav-item {{ $active === 'reports' ? 'active' : '' }}">
            <span class="icon">📋</span>
            <span>Denúncias</span>
        </a>

        <a href="{{ route('secretary.create') }}"
            class="nav-item {{ $active === 'secretaries' ? 'active' : '' }}">
            <span class="icon">👥</span>
            <span>Secretarias</span>
        </a>

        <a href="{{ route('category.create') }}"
            class="nav-item {{ $active === 'categories' ? 'active' : '' }}">
            <span class="icon">🏷️</span>
            <span>Categorias</span>
        </a>
    </nav>

    <form action="{{ route('logout') }}" method="post" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">
            <span class="icon">🚪</span>
            <span>Sair</span>
        </button>
    </form>
</aside>