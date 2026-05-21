@extends('layouts.app')

@section('title', 'Cadastrar Categoria')

@section('content')
<div style="max-width: 600px; margin: 30px auto; padding: 20px;">
    <h1>Cadastrar Nova Categoria</h1>

    @if ($errors->any())
        <div style="background: #fdecec; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fca5a5;">
            <strong>Erro ao validar:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('category.store') }}" method="POST" id="categoryForm">
        @csrf

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold;">
                Nome da Categoria
            </label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}"
                placeholder="Ex: Iluminação Pública"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;"
            >
            <span id="nameError" style="color: #991b1b; font-size: 12px; margin-top: 5px; display: block;">
                @error('name') {{ $message }} @enderror
            </span>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="secretary_id" style="display: block; margin-bottom: 5px; font-weight: bold;">
                Secretaria Responsável
            </label>
            <select 
                id="secretary_id" 
                name="secretary_id" 
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; background-color: white;"
            >
                <option value="">-- Selecione uma Secretaria --</option>
                @foreach($secretaries as $secretary)
                    <option value="{{ $secretary->id }}" {{ old('secretary_id') == $secretary->id ? 'selected' : '' }}>
                        {{ $secretary->name }}
                    </option>
                @endforeach
            </select>
            <span id="secretaryError" style="color: #991b1b; font-size: 12px; margin-top: 5px; display: block;">
                @error('secretary_id') {{ $message }} @enderror
            </span>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 5px; font-weight: bold;">
                Descrição
            </label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Ex: Problemas relacionados à iluminação pública da cidade (Mínimo 10 caracteres)"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; resize: vertical; min-height: 100px;"
            >{{ old('description') }}</textarea>
            <span id="descError" style="color: #991b1b; font-size: 12px; margin-top: 5px; display: block;">
                @error('description') {{ $message }} @enderror
            </span>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 40px;">
            <button 
                type="submit" 
                id="submitBtn"
                style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;"
            >
                Criar Categoria
            </button>

            <a 
                href="{{ route('admin.dashboard') }}" 
                style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block;"
            >
                Cancelar
            </a>
        </div>
    </form>

    <hr style="border: 0; border-top: 1px solid #ccc; margin-bottom: 20px;">
    
    <h2>Categorias Existentes</h2>
    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
        Total cadastradas no sistema: <strong>{{ count($categories) }}</strong>
    </p>

    <div style="display: flex; flex-direction: column; gap: 10px;">
        @forelse($categories as $cat)
            <div style="padding: 15px; border: 1px solid #eee; border-radius: 4px; background: #fafafa;">
                <strong style="display: block; font-size: 16px; margin-bottom: 5px;">{{ $cat->name }}</strong>
                <span style="font-size: 14px; color: #555;">{{ $cat->description }}</span>
            </div>
        @empty
            <p style="font-size: 14px; color: #666;">Nenhuma categoria cadastrada ainda.</p>
        @endforelse
    </div>

</div>

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

    function validateName() {
        const val = nameInput.value.trim();
        if (val.length === 0) {
            nameError.textContent = 'O nome é obrigatório.';
            return false;
        }
        if (val.length < 5 || val.length > 50) {
            nameError.textContent = 'Deve ter entre 5 e 50 caracteres.';
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
            descError.textContent = 'Deve ter entre 10 e 255 caracteres.';
            return false;
        }
        descError.textContent = '';
        return true;
    }

    function validateSec() {
        if (secInput.value === '') {
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
    secInput.addEventListener('change', checkFormValidity);

    form.addEventListener('submit', function(e) {
        const isNameValid = validateName();
        const isDescValid = validateDesc();
        const isSecValid = validateSec();

        if (!isNameValid || !isDescValid || !isSecValid) {
            e.preventDefault(); // Impede o envio do form
            alert('Por favor, corrija os erros no formulário antes de enviar.');
        }
    });

    // Inicia verificando as regras visualmente (útil caso a página recarregue com erros antigos)
    if(nameInput.value || descInput.value || secInput.value) {
        checkFormValidity();
    }
});
</script>
@endsection