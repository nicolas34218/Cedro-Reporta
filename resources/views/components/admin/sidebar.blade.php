@props(['active' => 'dashboard'])

@php
    $isSecretary = Auth::guard('secretary')->check();
@endphp

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <div class="logo-inner">
            <div class="logo-badge">⚠</div>
            <span class="logo-text">Cedro<strong>Reporta</strong></span>
        </div>
    </div>

    @if($isSecretary)
        <!-- Menu para Secretárias -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <p class="nav-section-title">PRINCIPAL</p>
                <a href="{{ route('secretary.dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('secretary.classify-reports') }}" class="nav-item {{ $active === 'classify' ? 'active' : '' }}">
                    <i class="fas fa-flag"></i>
                    <span>Classificar Denúncias</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="nav-item {{ $active === 'status' ? 'active' : '' }}">
                    <i class="fas fa-sync"></i>
                    <span>Atualizar Status</span>
                </a>
            </div>
        </nav>
    @else
        <!-- Menu para Admins -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <p class="nav-section-title">PRINCIPAL</p>
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
            </div>
        </nav>
    @endif

    <form action="{{ route('logout') }}" method="post" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </button>
    </form>
</aside>