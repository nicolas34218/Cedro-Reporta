<!-- Tela Criar Denúncia -->
 
@push('styles')
<link rel="stylesheet" href="{{ asset('css/citizen-report-create.css') }}">
@endpush
@extends('layouts.citizen')

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

    <div class="report-create-topbar">
        <a href="{{ auth()->check() ? route('citizen.home') : route('welcome') }}" class="btn-back">← Voltar</a>
        <h1>{{ $visitorMode ? 'Registrar Denúncia como Visitante' : 'Nova Denúncia' }}</h1>
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
                    <select name="category" id="category" class="form-control" required>
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('category') === $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
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
                        <i style="color: #2f6b3f;">💡</i> Preenchido automaticamente com base na categoria
                    </small>
                    <x-error-message field="secretary_id" />
                </div>

                @if($visitorMode)
                    <div class="form-field">
                        <label for="captcha_answer">Confirmação anti-bot: {{ $captchaQuestion }}</label>
                        <input id="captcha_answer" name="captcha_answer" type="text" value="{{ old('captcha_answer') }}">
                        <x-error-message field="captcha_answer" />
                    </div>
                @endif
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

@push('scripts')
<script>
    console.log('✨ Script de categoria carregado!');
    console.log('📍 Procurando elemento com ID: category');
    
    const categorySelect = document.getElementById('category');
    const secretarySelect = document.getElementById('secretary');
    
    console.log('✅ category encontrado:', categorySelect ? 'SIM' : 'NÃO');
    console.log('✅ secretary encontrado:', secretarySelect ? 'SIM' : 'NÃO');
    
    if (!categorySelect || !secretarySelect) {
        console.error('❌ Elementos não encontrados! Abortando script.');
    } else {
        // Carrega os setores responsáveis automaticamente ao mudar a categoria
        categorySelect.addEventListener('change', async function() {
            const categoryId = this.value;
            
            console.log('🔍 Categoria mudou para:', categoryId);
            
            if (!categoryId) {
                secretarySelect.innerHTML = '<option value="">Selecione uma categoria primeiro</option>';
                return;
            }

            try {
                secretarySelect.innerHTML = '<option value="">Carregando...</option>';
                
                const url = `/api/categories/${categoryId}/secretaries`;
                console.log('📡 Fazendo fetch para:', url);
                
                // Faz requisição para buscar os setores responsáveis da categoria
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                console.log('📊 Status da resposta:', response.status);
                console.log('📦 Response OK?', response.ok);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('❌ Erro na resposta:', errorText);
                    throw new Error(`Erro ao carregar setores (Status: ${response.status})`);
                }

                const secretaries = await response.json();
                console.log('✅ Secretárias recebidas:', secretaries);
                
                if (secretaries.length === 0) {
                    console.warn('⚠️ Nenhuma secretária encontrada para esta categoria');
                    secretarySelect.innerHTML = '<option value="">Nenhum setor responsável configurado</option>';
                    return;
                }

                // Preenche o select com os setores
                secretarySelect.innerHTML = '<option value="">Selecione (opcional)</option>';
                secretaries.forEach(secretary => {
                    console.log(`➕ Adicionando secretária: ${secretary.name} (ID: ${secretary.id})`);
                    const option = document.createElement('option');
                    option.value = secretary.id;
                    option.textContent = secretary.name;
                    secretarySelect.appendChild(option);
                });

                // Se há apenas 1 secretária, seleciona automaticamente
                if (secretaries.length === 1) {
                    secretarySelect.value = secretaries[0].id;
                    console.log('🎯 Secretária auto-selecionada:', secretaries[0].name);
                } else {
                    console.log('⚙️ Múltiplas secretárias disponíveis, usuário deve escolher');
                }

                console.log('🎉 Select preenchido com sucesso');

            } catch (error) {
                console.error('🚨 Erro ao carregar setores:', error);
                secretarySelect.innerHTML = '<option value="">Erro ao carregar setores</option>';
            }
        });
        
        console.log('✅ Event listener adicionado com sucesso');
    }
</script>
@endpush
