<!-- Tela Criar Denúncia -->
 
@push('styles')
<link rel="stylesheet" href="{{ asset('css/citizen-report-create.css') }}">
@endpush
@extends('layouts.citizen')

@section('title', 'Registrar Denúncia')

@section('content')
<section class="report-create-page">
    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: #fdecec; color: #991b1b; border: 1px solid #fca5a5;">
            {{ session('error') }}
        </div>
    @endif

    <div class="report-create-topbar">
        <a href="{{ route('citizen.home') }}" class="btn-back">← Voltar</a>
        <h1>Nova Denúncia</h1>
    </div>

    <form action="{{ route('citizen.reports.store') }}" method="post" class="report-form" enctype="multipart/form-data">
        @csrf

        <div class="report-grid">
            <div class="form-column">
                <h2><span class="step">1</span> Informações básicas</h2>

                <div class="form-field">
                    <label for="title">TÍTULO DA DENÚNCIA</label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}">
                    <x-error-message field="title" />
                </div>

                <div class="form-field">
                    <label for="description">DESCRIÇÃO</label>
                    <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    <x-error-message field="description" />
                </div>

                <div class="form-field">
                    <label for="category">CATEGORIA</label>
                    <select id="category" name="category">
                        <option value="">Selecione</option>
                        <option value="Iluminação" @selected(old('category') === 'Iluminação')>Iluminação</option>
                        <option value="Buracos" @selected(old('category') === 'Buracos')>Buracos</option>
                        <option value="Lixo" @selected(old('category') === 'Lixo')>Lixo</option>
                        <option value="Segurança" @selected(old('category') === 'Segurança')>Segurança</option>
                        <option value="Outros" @selected(old('category') === 'Outros')>Outros</option>
                    </select>
                    <x-error-message field="category" />
                </div>
            </div>

            <div class="form-column">
                <h2><span class="step">2</span> Localização</h2>

                <div class="form-field">
                    <label for="address_reference">ENDEREÇO/REFERÊNCIA <span style="color: #d32f2f;">*</span></label>
                    <input id="address_reference" name="address_reference" type="text" value="{{ old('address_reference') }}" placeholder="Ex: Rua Principal, próximo ao mercado">
                    <x-error-message field="address_reference" />
                </div>

                <div class="form-field">
                    <label for="district">BAIRRO <span style="color: #d32f2f;">*</span></label>
                    <input id="district" name="district" type="text" value="{{ old('district') }}" placeholder="Ex: Centro">
                    <x-error-message field="district" />
                </div>
            </div>

            <div class="form-column">
                <h2><span class="step">3</span> Fotos e evidências</h2>
                <div class="form-field">
                    <label for="image">UPLOAD DE IMAGEM (PNG, JPG ou JPEG)</label>
                    <input id="image" name="image" type="file" accept="image/png,image/jpeg" aria-describedby="image-help">
                    <small id="image-help" style="display: block; margin-top: 8px; color: #666;">Formatos aceitos: PNG, JPG, JPEG. Tamanho máximo: 2MB</small>
                    <x-error-message field="image" />
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">Enviar Denúncia</button>
    </form>
</section>
@endsection