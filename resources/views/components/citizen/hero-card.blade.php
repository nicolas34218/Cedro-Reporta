<!--
    Este componente exibe um cartão de boas-vindas para os usuários, incentivando-os a criar uma conta ou fazer login para acessar os recursos completos do sistema.
-->
<div class="hero-card">

    <div class="hero-left">

        <div class="hero-badge">

            <img
                src="{{ asset('logo-cedro.png') }}"
                alt="Logo CedroReporta">

        </div>

        <div>

            <h2>{{ $title }}</h2>

            <p>{{ $description }}</p>

        </div>

    </div>

    <a href="{{ $buttonLink }}"
       class="hero-btn">

        {{ $buttonText }}

    </a>

</div>