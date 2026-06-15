@extends('layouts.citizen')

@section('title', 'Modo Visitante')

@section('content')

<div class="home-page-wrapper">

    <section class="home-dashboard">

        <x-citizen.sidebar :visitorMode="true" />

        <main class="home-content">

            <h1 class="home-title">
                Home
            </h1>

            <x-citizen.visitor-warning />

            <p class="home-subtitle">
                Você está navegando como visitante
            </p>

            <div class="visitor-alert">
                Algumas funcionalidades estão bloqueadas.
                Crie uma conta para acompanhar suas denúncias.
            </div>

            <div class="hero-card">

                <div class="hero-left">

                    <div>
                        <h2>Faça sua cidade melhor</h2>

                        <p>
                            Registre problemas urbanos de forma rápida.
                        </p>

                    </div>

                </div>

                <a
                    href="{{ route('citizen.reports.create') }}"
                    class="hero-btn">

                    Nova Denúncia

                </a>

            </div>

        </main>

    </section>

</div>

@endsection