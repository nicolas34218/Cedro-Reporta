@props(['active' => 'dashboard'])

@php
    $isSecretary = Auth::guard('secretary')->check();
    $unclassifiedCount = 0;

    // Se for secretária, busca quantas denúncias ela tem sem prioridade definida
    if ($isSecretary) {
        $unclassifiedCount = \App\Models\Report::where('secretary_id', Auth::guard('secretary')->id())
            ->whereNull('priority')
            ->count();
    }
@endphp

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <div class="logo-inner">
            <div class="logo-badge">⚠</div>
            <span class="logo-text">Cedro<strong>Reporta</strong></span>
        </div>
    </div>

    @if($isSecretary)
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
                    @if($unclassifiedCount > 0)
                        <span id="sidebar-unclassified-count" style="background-color: #ef4444; color: white; font-size: 12px; font-weight: bold; padding: 2px 8px; border-radius: 999px; margin-left: auto;">
                            {{ $unclassifiedCount }}
                        </span>
                    @endif
                </a>
                    <i class="fas fa-sync"></i>
                    <span>Atualizar Status</span>
                </a>
            </div>
        </nav>
    @else
        <nav class="sidebar-nav">
            <div class="nav-section">
                <p class="nav-section-title">PRINCIPAL</p>
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
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