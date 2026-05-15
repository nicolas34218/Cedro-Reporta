@props(['active' => 'dashboard'])

<aside class="admin-sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.reports') }}" class="nav-item {{ $active === 'reports' ? 'active' : '' }}">
            <i class="fas fa-file-alt"></i>
            <span>Denúncias</span>
        </a>

        <a href="{{ route('secretary.create') }}" class="nav-item {{ $active === 'secretaries' ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Secretarias</span>
        </a>

        <a href="{{ route('category.create') }}" class="nav-item {{ $active === 'categories' ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>Categorias</span>
        </a>
    </nav>

    <form action="{{ route('logout') }}" method="post" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </button>
    </form>
</aside>