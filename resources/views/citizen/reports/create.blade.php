<!-- Tela Criar Den�ncia -->
 
@push('styles')
<link rel="stylesheet" href="{{ asset('css/citizen-report-create.css') }}">
@endpush
@extends('layouts.citizen')

@section('title', $visitorMode ? 'Registrar Den�ncia como Visitante' : 'Registrar Den�ncia')

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

    <div class="report-create-topbar">
        <a href="{{ auth()->check() ? route('citizen.home') : route('welcome') }}" class="btn-back">? Voltar</a>
        <h1>{{ $visitorMode ? 'Registrar Den�ncia como Visitante' : 'Nova Den�ncia' }}</h1>
    </div>

    @if($visitorMode)
        <div class="visitor-note" style="margin-bottom: 24px; padding: 16px; border-radius: 12px; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1;">
            Voc� est� enviando uma den�ncia como visitante. Sua den�ncia ser� registrada, mas voc� n�o poder� receber notifica��es autom�ticas nem acompanhar o status pelo sistema.
        </div>
    @endif

    <form action="{{ $formAction }}" method="post" class="report-form" enctype="multipart/form-data">
        @csrf

        <div class="report-grid">
            <div class="form-column">
                <h2><span class="step">1</span> Informa��es b�sicas</h2>

                <div class="form-field">
                    <label for="title">T�TULO DA DEN�NCIA</label>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('category') === $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-error-message field="category" />
                </div>

                <div class="form-field">
                    <label for="secretary">SETOR RESPONS�VEL</label>
                    <select id="secretary" name="secretary_id">
                        <option value="">Carregando...</option>
                    </select>
                    <small style="display: block; margin-top: 8px; color: #666;">
                        <i style="color: #2f6b3f;">??</i> Preenchido automaticamente com base na categoria
                    </small>
                    <x-error-message field="secretary_id" />
                </div>

                    <div class="form-field">
                        <label for="captcha_answer">
                            Confirma��o anti-bot: {{ $captchaQuestion }}
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
                <h2><span class="step">2</span> Localiza��o</h2>

                <div class="form-field">
                    <label for="address_reference">ENDERE�O/REFER�NCIA <span style="color: #d32f2f;">*</span></label>
                    <input id="address_reference" name="address_reference" type="text" value="{{ old('address_reference') }}" placeholder="Ex: Rua Principal, pr�ximo ao mercado">
                    <x-error-message field="address_reference" />
                </div>

                <div class="form-field">
                    <label for="district">BAIRRO <span style="color: #d32f2f;">*</span></label>
                    <input id="district" name="district" type="text" value="{{ old('district') }}" placeholder="Ex: Centro">
                    <x-error-message field="district" />
                </div>
            </div>

            <div class="form-column">
                <h2><span class="step">3</span> Fotos e evid�ncias</h2>
                <div class="form-field">
                    <label for="image">UPLOAD DE IMAGEM (PNG, JPG ou JPEG)</label>
                    <input id="image" name="image" type="file" accept="image/png,image/jpeg" aria-describedby="image-help">
                    <small id="image-help" style="display: block; margin-top: 8px; color: #666;">Formatos aceitos: PNG, JPG, JPEG. Tamanho m�ximo: 2MB</small>
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
                    Manter den�ncia an�nima
                </label>

            </div>

            <p>
                Seus dados pessoais n�o ser�o exibidos para
                a secretaria respons�vel pela den�ncia.
            </p>

        </div>
        @endunless

        <button type="submit" class="btn-submit">Enviar Den�ncia</button>
    </form>
</section>

@push('scripts')
<script>
    console.log('? Script de categoria carregado!');
    console.log('?? Procurando elemento com ID: category');
    
    const categorySelect = document.getElementById('category');
    const secretarySelect = document.getElementById('secretary');
    
    console.log('? category encontrado:', categorySelect ? 'SIM' : 'N�O');
    console.log('? secretary encontrado:', secretarySelect ? 'SIM' : 'N�O');
    
    if (!categorySelect || !secretarySelect) {
        console.error('? Elementos n�o encontrados! Abortando script.');
    } else {
        // Carrega os setores respons�veis automaticamente ao mudar a categoria
        categorySelect.addEventListener('change', async function() {
            const categoryId = this.value;
            
            console.log('?? Categoria mudou para:', categoryId);
            
            if (!categoryId) {
                secretarySelect.innerHTML = '<option value="">Selecione uma categoria primeiro</option>';
                return;
            }

            try {
                secretarySelect.innerHTML = '<option value="">Carregando...</option>';
                
                const url = `/api/categories/${categoryId}/secretaries`;
                console.log('?? Fazendo fetch para:', url);
                
                // Faz requisi��o para buscar os setores respons�veis da categoria
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                console.log('?? Status da resposta:', response.status);
                console.log('?? Response OK?', response.ok);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('? Erro na resposta:', errorText);
                    throw new Error(`Erro ao carregar setores (Status: ${response.status})`);
                }

                const secretaries = await response.json();
                console.log('? Secret�rias recebidas:', secretaries);
                
                if (secretaries.length === 0) {
                    console.warn('?? Nenhuma secret�ria encontrada para esta categoria');
                    secretarySelect.innerHTML = '<option value="">Nenhum setor respons�vel configurado</option>';
                    return;
                }

                // Preenche o select com os setores
                secretarySelect.innerHTML = '<option value="">Selecione (opcional)</option>';
                secretaries.forEach(secretary => {
                    console.log(`? Adicionando secret�ria: ${secretary.name} (ID: ${secretary.id})`);
                    const option = document.createElement('option');
                    option.value = secretary.id;
                    option.textContent = secretary.name;
                    secretarySelect.appendChild(option);
                });

                // Se h� apenas 1 secret�ria, seleciona automaticamente
                if (secretaries.length === 1) {
                    secretarySelect.value = secretaries[0].id;
                    console.log('?? Secret�ria auto-selecionada:', secretaries[0].name);
                } else {
                    console.log('?? M�ltiplas secret�rias dispon�veis, usu�rio deve escolher');
                }

                console.log('?? Select preenchido com sucesso');

            } catch (error) {
                console.error('?? Erro ao carregar setores:', error);
                secretarySelect.innerHTML = '<option value="">Erro ao carregar setores</option>';
            }
        });
        
        console.log('? Event listener adicionado com sucesso');
    }
</script>
@endpush

