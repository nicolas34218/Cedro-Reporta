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

            <form action="{{ route('category.store') }}" method="POST" class="category-form" id="categoryForm">
                @csrf

                <div class="field">
                    <label for="name">Nome da categoria</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Ex: Iluminação Pública">
                    <small class="error" id="nameError" style="color: #991b1b; display: block; margin-top: 5px;">
                        @error('name') {{ $message }} @enderror
                    </small>
                </div>

                <div class="field">
                    <label for="secretary_id">Secretaria Responsável</label>
                    <select id="secretary_id" name="secretary_id" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-family: inherit; font-size: 1rem; background-color: white;">
                        <option value="">-- Selecione uma Secretaria --</option>
                        @isset($secretaries)
                            @foreach($secretaries as $secretary)
                                <option value="{{ $secretary->id }}" {{ old('secretary_id') == $secretary->id ? 'selected' : '' }}>
                                    {{ $secretary->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    <small class="error" id="secretaryError" style="color: #991b1b; display: block; margin-top: 5px;">
                        @error('secretary_id') {{ $message }} @enderror
                    </small>
                </div>

                <div class="field">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" placeholder="Ex: Problemas relacionados à iluminação pública da cidade (Mínimo 10 caracteres)">{{ old('description') }}</textarea>
                    <small class="error" id="descError" style="color: #991b1b; display: block; margin-top: 5px;">
                        @error('description') {{ $message }} @enderror
                    </small>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">Cadastrar Categoria</button>
            </form>
        </div>

        <div class="category-list-column">
            <h2 class="list-title">CATEGORIAS EXISTENTES ({{ $categories->count() }})</h2>

            <div class="categories-toolbar">

                <div class="search-box">
                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        id="category-search"
                        placeholder="Pesquisar categoria...">
                </div>

            </div>

            <p id="no-results">
                <i class="bi bi-search"></i>
                Nenhuma categoria encontrada.
            </p>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    const nameInput = document.getElementById('name');
    const descInput = document.getElementById('description');
    const secInput = document.getElementById('secretary_id');
    const submitBtn = document.getElementById('submitBtn');

    const nameError = document.getElementById('nameError');
    const descError = document.getElementById('descError');
    const secError = document.getElementById('secretaryError');

    const nameRegex = /^[a-zA-ZÀ-ÿ\s]+$/;

    const searchInput = document.getElementById('category-search');
    const categories = document.querySelectorAll('.category-item');
    const noResults = document.getElementById('no-results');

    searchInput.addEventListener('input', function () {

        const filter = this.value.toLowerCase();
        let visibleCount = 0;

        categories.forEach(item => {

            const text = item.innerText.toLowerCase();

            if (text.includes(filter)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        noResults.style.display =
            visibleCount === 0 ? 'block' : 'none';
    });

    function validateName() {
        const val = nameInput.value.trim();
        if (val.length === 0) {
            nameError.textContent = 'O nome da categoria é obrigatório.';
            return false;
        }
        if (val.length < 5 || val.length > 50) {
            nameError.textContent = 'O nome deve ter entre 5 e 50 caracteres.';
            return false;
        }
        if (!nameRegex.test(val)) {
            nameError.textContent = 'Apenas letras e espaços são permitidos (sem números/símbolos).';
            return false;
        }
        nameError.textContent = '';
        return true;
    }

    function validateDesc() {
        const val = descInput.value.trim();
        if (val.length === 0) {
            descError.textContent = 'A descrição é obrigatória.';
            return false;
        }
        if (val.length < 10 || val.length > 255) {
            descError.textContent = 'A descrição deve ter entre 10 e 255 caracteres.';
            return false;
        }
        descError.textContent = '';
        return true;
    }

    function validateSec() {
        if (!secInput || secInput.value === '') {
            secError.textContent = 'Selecione uma secretaria responsável.';
            return false;
        }
        secError.textContent = '';
        return true;
    }

    function checkFormValidity() {
        const isNameValid = validateName();
        const isDescValid = validateDesc();
        const isSecValid = validateSec();
        
        submitBtn.style.opacity = (isNameValid && isDescValid && isSecValid) ? '1' : '0.6';
    }

    nameInput.addEventListener('input', checkFormValidity);
    descInput.addEventListener('input', checkFormValidity);
    if(secInput) {
        secInput.addEventListener('change', checkFormValidity);
    }

    form.addEventListener('submit', function(e) {
        const isNameValid = validateName();
        const isDescValid = validateDesc();
        const isSecValid = validateSec();

        if (!isNameValid || !isDescValid || !isSecValid) {
            e.preventDefault();
            alert('Por favor, corrija os erros assinalados no formulário antes de enviar.');
        }
    });

    // Validar caso a página tenha sido recarregada com os inputs preenchidos
    if (nameInput.value || descInput.value || (secInput && secInput.value)) {
        checkFormValidity();
    }
});
</script>
@endsection