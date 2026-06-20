@props([
    'active' => 'dashboard',
    'pendingCount' => 0
])

@php
    $isSecretary = Auth::guard('secretary')->check();
@endphp

<aside class="secretary-sidebar">
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
                
                <a href="{{ route('secretary.classify-reports') }}"
                    class="nav-item {{ $active === 'classify' ? 'active' : '' }}">

                    <i class="fas fa-flag"></i>

                    <span>Classificar Denúncias</span>

                    @if($pendingCount > 0)
                        <span class="menu-badge">
                            {{ $pendingCount }}
                        </span>
                    @endif

                </a>

                <a href="{{ route('secretary.share.index') }}"
                    class="nav-item {{ $active === 'share' ? 'active' : '' }}">
                    <i class="fas fa-share-alt"></i>
                    <span>Compartilhar Denúncia</span>
                </a>

                <a href="{{ route('secretary.transfer.index') }}"
                    class="nav-item {{ $active === 'transfer' ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transferir Denúncia</span>
                </a>
            </div>
        </nav>
    @else
        <!-- Menu para Admins -->
        <nav class="sidebar-nav">
            <div class="nav-section">
                <p class="nav-section-title">PRINCIPAL</p>
                <a href="{{ route('secretary.dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
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