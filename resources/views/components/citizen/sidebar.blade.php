<aside class="home-sidebar" id="home-sidebar">

@props([
    'visitorMode' => false
])

    <div class="sidebar-menu">
        <h3 class="sidebar-title">MENU</h3>

        <a href="{{ ($visitorMode ?? false)
            ? route('visitor.home')
            : route('citizen.home') }}"
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

        <a href="{{ ($visitorMode ?? false)
            ? route('visitor.reports.create')
            : route('citizen.reports.create') }}"
            class="sidebar-link">

            <i class="bi bi-plus-circle"></i>
            <span>Nova Denúncia</span>
        </a>

    </div>

    <!-- Área de logout ou registro, dependendo do modo visitante -->

   @if($visitorMode ?? false)

        <div class="sidebar-logout">
            <a href="{{ route('register') }}"
            class="visitor-register-btn">
                Criar Conta
            </a>
        </div>

    @else

        <form action="{{ route('logout') }}"
            method="post"
            class="sidebar-logout">
            @csrf

            <button type="submit"
                    class="sidebar-logout-btn">

                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>

            </button>
        </form>

    @endif

</aside>