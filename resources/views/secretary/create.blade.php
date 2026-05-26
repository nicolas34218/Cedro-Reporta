@extends('layouts.admin', ['active' => 'secretaries'])

@section('title', 'Cadastrar Secretária')
@section('page-title', 'Cadastro de Secretaria')
@section('page-subtitle', 'Cadastre as secretarias responsáveis pelo atendimento das denúncias')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/secretary-create.css') }}">
@endpush

@section('content')
    <section class="secretary-grid">
        <div class="secretary-card secretary-form-card">
            <h2 class="card-title">NOVA SECRETARIA</h2>

            <form action="{{ route('secretary.store') }}" method="POST" class="secretary-form">
                @csrf

                <div class="field">
                    <label for="name">Nome da secretaria</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}">
                    @error('name')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password">
                    @error('password')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmar senha</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                    @error('password_confirmation')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Cadastrar Secretaria</button>
            </form>
        </div>

        <div class="secretary-list-column">
            <h2 class="list-title">SECRETARIAS EXISTENTES ({{ $secretaries->count() }})</h2>

            <div class="secretary-list">
                @forelse ($secretaries as $secretary)
                    <article class="secretary-item">
                        <strong>{{ $secretary->name }}</strong>
                        <p style="font-size: 13px; color: #666;">{{ $secretary->email }}</p>
                    </article>
                @empty
                    <article class="secretary-item empty-state">
                        <strong>Nenhuma secretaria cadastrada ainda.</strong>
                    </article>
                @endforelse
            </div>
        </div>
    </section>
@endsection