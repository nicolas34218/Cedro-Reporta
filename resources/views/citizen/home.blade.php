@extends('layouts.citizen')

@section('title', 'Tela principal')

@section('content')
<section class="home-main">
    <div class="home-card">
        <h1>Tela principal</h1>
        <p class="subtitle">Bem-vindo(a)! Aqui você terá acesso às suas funcionalidades.</p>

        <!-- Exemplo de botões/áreas futuras -->
        <div class="home-actions">
            <a href="{{ route('citizen.reports.create') }}" class="btn-primary">Fazer denúncia</a>
            <a href="#" class="btn-outline">Minhas Denúncias</a>
        </div>
    </div>
</section>
@endsection