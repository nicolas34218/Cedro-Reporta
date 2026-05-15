@extends('layouts.admin', ['active' => 'categories'])

@section('title', 'Cadastrar Categoria')
@section('page-title', 'Cadastro de Categorias')
@section('page-subtitle', 'Cadastre novas categorias para organizar os tipos de problemas reportados.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/category-create.css') }}">
@endpush

@section('content')
    <section class="category-grid">
        <div class="category-card category-form-card">
            <h2 class="card-title">NOVA CATEGORIA</h2>

            <form action="{{ route('category.store') }}" method="POST" class="category-form">
                @csrf

                <div class="field">
                    <label for="name">Nome da categoria</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ex: Iluminação Pública">
                    @error('name')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" placeholder="Ex: Problemas relacionados à iluminação pública da cidade">{{ old('description') }}</textarea>
                    @error('description')
                        <small class="error">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Cadastrar Categoria</button>
            </form>
        </div>

        <div class="category-list-column">
            <h2 class="list-title">CATEGORIA EXISTENTES({{ $categories->count() }})</h2>

            <div class="category-list">
                @forelse ($categories as $category)
                    <article class="category-item">
                        <strong>{{ $category->name }}</strong>
                        <p>{{ $category->description ?? 'Sem descrição' }}</p>
                    </article>
                @empty
                    <article class="category-item empty-state">
                        <strong>Nenhuma categoria cadastrada ainda.</strong>
                    </article>
                @endforelse
            </div>
        </div>
    </section>
@endsection
