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

    <form action="{{ route('category.store') }}" method="POST">
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
                required
            >
            @error('name')
                <span style="color: #991b1b; font-size: 12px; margin-top: 5px; display: block;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="description" style="display: block; margin-bottom: 5px; font-weight: bold;">
                Descrição (Opcional)
            </label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Ex: Problemas relacionados à iluminação pública da cidade"
                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; resize: vertical; min-height: 100px;"
            >{{ old('description') }}</textarea>
            @error('description')
                <span style="color: #991b1b; font-size: 12px; margin-top: 5px; display: block;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div style="display: flex; gap: 10px;">
            <button 
                type="submit" 
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
</div>
@endsection
