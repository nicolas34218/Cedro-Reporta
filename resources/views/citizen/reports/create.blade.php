<!-- Tela Criar Denúncia -->
 
@extends('layouts.citizen')

@php
    $hideHeader = true;
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/citizen-report-create.css') }}">

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>
@endpush

@section('title', $visitorMode ? 'Registrar Denúncia como Visitante' : 'Registrar Denúncia')

@section('content')

<section class="report-create-page">
    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: #fdecec; color: #991b1b; border: 1px solid #fca5a5;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('visitor_notice'))
        <div class="alert alert-info" style="margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; background: #eff6ff; color: #1d4ed8; border: 1px solid #93c5fd;">
            {{ session('visitor_notice') }}
        </div>
    @endif

        <div class="report-create-header">

            <a href="{{ auth()->check()
                ? route('citizen.home')
                : route('welcome') }}"
                class="btn-back">

                <i class="bi bi-arrow-left"></i>
                Voltar

            </a>

            <div>
                <h1>
                    {{ $visitorMode
                        ? 'Registrar Denúncia como Visitante'
                        : 'Nova Denúncia' }}
                </h1>

                <p>
                    Preencha os dados abaixo para registrar sua denúncia.
                </p>
            </div>

        </div>

    @if($visitorMode)
        <div class="visitor-note" style="margin-bottom: 24px; padding: 16px; border-radius: 12px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1;">
            Você está enviando uma denúncia como visitante. Sua denúncia será registrada, mas você não poderá receber notificações automáticas nem acompanhar o status pelo sistema.
        </div>
    @endif

    <form action="{{ $formAction }}" method="post" class="report-form" enctype="multipart/form-data">
        @csrf

                    <div class="report-grid">
                        <div class="form-column">
                            <h2><span class="step">1</span> Informações básicas</h2>

                        <div class="form-field">
                            <label for="title">
                                TÍTULO DA DENÚNCIA <span style="color: #d32f2f;">*</span>
                            </label>

                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                placeholder="Ex: Buraco na Rua Principal">

                            <x-error-message field="title" />
                        </div>


                        <div class="form-field">
                            <label for="description">
                                DESCRIÇÃO DA DENÚNCIA <span style="color:red">*</span>
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                placeholder="Descreva detalhadamente o problema encontrado...">{{ old('description') }}</textarea>

                            <x-error-message field="description" />
                        </div>

                        <div class="form-field">
                            <label for="category">
                                CATEGORIA DA DENÚNCIA
                            </label>

                            <select id="category" name="category">
                                <option value="">
                                    Selecione uma categoria
                                </option>

                                @foreach($categories as $category)
                                    <option
                                        value="{{ $category->name }}"
                                        {{ old('category') === $category->name ? 'selected' : '' }}>

                                        {{ $category->name }}

                                    </option>
                                @endforeach
                            </select>

                            <x-error-message field="category" />
                        </div>


                        <div class="form-field">
                            <label for="secretary">SETOR RESPONSÁVEL</label>
                            <select id="secretary" name="secretary_id">
                                <option value="">Carregando...</option>
                            </select>
                            <small style="display: block; margin-top: 8px; color: #666;">
                                <i style="color: #2f6b3f;"></i> Preenchido automaticamente com base na categoria
                            </small>
                            <x-error-message field="secretary_id" />
                        </div>

                        <div class="form-field">
                            <label for="captcha_answer">
                                Confirmação anti-bot: {{ $captchaQuestion }}
                            </label>

                            <input
                                id="captcha_answer"
                                name="captcha_answer"
                                type="text"
                                value="{{ old('captcha_answer') }}">

                            <x-error-message field="captcha_answer" />
                        </div>
            </div>

            <div class="form-column">
                <h2><span class="step">2</span> Localização</h2>

                <div class="form-field">
                    <label>
                        Clique no mapa para selecionar o local exato
                    </label>

                    <div id="map"></div>
                </div>

                <div class="form-field">
                    <label>Endereço selecionado</label>

                    <input
                        type="text"
                        id="location_address"
                        name="location_address"
                        readonly
                        placeholder="Clique no mapa para selecionar">
                </div>

                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">

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

        @unless($visitorMode)
        <div class="anonymous-box">

            <div class="anonymous-header">

                <input
                    type="checkbox"
                    id="anonymous"
                    name="anonymous"
                    value="1"
                    {{ old('anonymous') ? 'checked' : '' }}>

                <label for="anonymous">
                    Manter denúncia anônima
                </label>

            </div>

            <p>
                Seus dados pessoais não serão exibidos para
                a secretaria responsável pela denúncia.
            </p>

        </div>
        @endunless

        <button type="submit" class="btn-submit">Enviar Denúncia</button>
    </form>
</section>

@push('scripts')

<script>
    console.log('Script de categoria carregado!');

    const categorySelect = document.getElementById('category');
    const secretarySelect = document.getElementById('secretary');

    if (!categorySelect || !secretarySelect) {
        console.error('Elementos não encontrados!');
    } else {

        categorySelect.addEventListener('change', async function() {

            const categoryId = this.value;

            if (!categoryId) {
                secretarySelect.innerHTML =
                    '<option value="">Selecione uma categoria primeiro</option>';
                return;
            }

            try {

                secretarySelect.innerHTML =
                    '<option value="">Carregando...</option>';

                const response = await fetch(
                    `/api/categories/${categoryId}/secretaries`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    }
                );

                const secretaries = await response.json();

                secretarySelect.innerHTML =
                    '<option value="">Selecione (opcional)</option>';

                secretaries.forEach(secretary => {

                    const option =
                        document.createElement('option');

                    option.value = secretary.id;
                    option.textContent = secretary.name;

                    secretarySelect.appendChild(option);
                });

            } catch (error) {
                console.error(error);
            }

        });
    }
</script>

<!-- Script externo do Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Script do mapa -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    console.log('Leaflet carregado?', typeof L);

    const map = L.map('map').setView([-6.6068, -39.0628], 14);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            attribution: '&copy; OpenStreetMap'
        }
    ).addTo(map);

    let marker;

    map.on('click', function(e) {

        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map);

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Busca o endereço correspondente às coordenadas
        fetch(`{{ route('reports.location.resolve') }}?latitude=${lat}&longitude=${lng}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {

            console.log('Endereço retornado:', data);

            document.getElementById('location_address').value =
                data.address || 'Endereço não encontrado';
        })
        .catch(error => {

            console.error('Erro ao buscar endereço:', error);

            document.getElementById('location_address').value =
                'Erro ao obter endereço';
        });

    });

});
</script>

@endpush

