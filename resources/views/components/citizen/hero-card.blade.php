<!--
    Este componente exibe um cartão de boas-vindas para os usuários, incentivando-os a criar uma conta ou fazer login para acessar os recursos completos do sistema.
-->
<div class="hero-card">

    <div class="hero-left">

        <div class="hero-badge">

            <img
                src="{{ asset('logo-cedro.png') }}"
                alt="Prefeitura">

        </div>

        <div>

            <h2>Faça sua cidade melhor</h2>

            <p>
                Registre problemas urbanos e acompanhe as melhorias.
            </p>

        </div>

    </div>

    <a
        href="{{ $visitorMode
        ? route('visitor.reports.create')
        : route('citizen.reports.create') }}"
        class="hero-btn">

        Nova Denúncia

    </a>

</div>