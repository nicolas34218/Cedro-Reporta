<aside class="home-sidebar" id="home-sidebar">
    <button
        id="home-sidebar-close"
        class="home-sidebar-close"
        aria-label="Fechar menu">
        ×
    </button>

    <div class="sidebar-menu">
        <h3 class="sidebar-title">MENU</h3>

        <a href="{{ route('citizen.home') }}"
           class="sidebar-link">

            <i class="bi bi-house-door"></i>
            <span>Home</span>
        </a>

        @unless($visitorMode ?? false)

            <a href="{{ route('citizen.reports.index') }}"
               class="sidebar-link">

                <i class="bi bi-file-earmark-text"></i>
                <span>Minhas Denúncias</span>
            </a>

            <a href="{{ route('citizen.reports.track-status', 1) }}"
               class="sidebar-link">

                <i class="bi bi-clock-history"></i>
                <span>Acompanhar Status</span>
            </a>

        @endunless

        <a href="{{ route('citizen.reports.create') }}"
           class="sidebar-link">

            <i class="bi bi-plus-circle"></i>
            <span>Nova Denúncia</span>
        </a>

    </div>

    @unless($visitorMode ?? false)
        <form action="{{ route('logout') }}" method="post" class="sidebar-logout">
            @csrf

            <button type="submit" class="sidebar-logout-btn">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </button>
        </form>
    @endunless
</aside>