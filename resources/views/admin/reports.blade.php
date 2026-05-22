<!-- Página de Classificar denúncias - Tela Secretária -->

@extends('layouts.admin', ['active' => 'classify'])

@push('styles')
<link rel='stylesheet' href='/css/admin/reports.css'>
@endpush

@section('content')
    <div class="classify-header">
        <h1 class="page-title">Classificação de Denúncias</h1>
        <p class="page-subtitle">Defina a prioridade das denúncias conforme a gravidade de cada ocorrência.</p>
    </div>

    <!-- Cards de Estatísticas -->
    <section class="statistics-section">
        <div class="stat-card">
            <p class="stat-label">DENÚNCIAS RECEBIDAS</p>
            <h2 class="stat-number">{{ $statistics['total_reports'] ?? 0 }}</h2>
            <div class="stat-underline"></div>
        </div>

        <div class="stat-card pending">
            <p class="stat-label">PENDENTE</p>
            <h2 class="stat-number">{{ $statistics['pending_reports'] ?? 0 }}</h2>
            <div class="stat-underline"></div>
        </div>

        <div class="stat-card in-progress">
            <p class="stat-label">EM ANDAMENTO</p>
            <h2 class="stat-number">{{ $statistics['analyzing_reports'] ?? 0 }}</h2>
            <div class="stat-underline"></div>
        </div>
    </section>

    <!-- Seção de Denúncias -->
    <section class="reports-list-section">
        <h3 class="section-title">DENÚNCIAS</h3>

        @if($reports->isEmpty())
            <div class="no-reports-message">
                <i class="fas fa-inbox"></i>
                <p>Nenhuma denúncia para classificar.</p>
            </div>
        @else
        <div class="reports-cards-container">
            @foreach($reports as $report)
                <div class="report-card">
                    <div class="report-card-content">
                        <h4 class="report-title">{{ $report->title }}</h4>
                        <div class="report-meta">
                            <span class="report-location">
                                <i class="fas fa-map-pin"></i>
                                {{ $report->location ?? 'Sem localização' }}
                            </span>
                            <span class="report-date">{{ $report->created_at->format('d/m/Y') }}</span>
                        </div>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                                {{ $report->status }}
                            </span>
                        </div>


                        <div class="report-card-action">
                            <select class="priority-select" onchange="updatePriority({{ $report->id }}, this.value)">
                                <option value="">Selecione Prioridade</option>
                                <option value="Alta" {{ $report->priority === 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Média" {{ $report->priority === 'Média' ? 'selected' : '' }}>Média</option>
                                <option value="Baixa" {{ $report->priority === 'Baixa' ? 'selected' : '' }}>Baixa</option>
                            </select>

                            <select class="status-select" onchange="updateStatus({{ $report->id }}, this.value, '{{ Auth::guard('secretary')->check() ? 'secretary' : 'admin' }}')">
                                <option value="">Selecione Status</option>
                                <option value="Pendente" {{ $report->status === 'Pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="Em Análise" {{ $report->status === 'Em Análise' ? 'selected' : '' }}>Em Análise</option>
                                <option value="Resolvida" {{ $report->status === 'Resolvida' ? 'selected' : '' }}>Resolvida</option>
                                <option value="Fechada" {{ $report->status === 'Fechada' ? 'selected' : '' }}>Fechada</option>
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!token) {
            console.error('CSRF token não encontrado!');
            throw new Error('Token CSRF não encontrado');
        }
        return token;
    }

    function updatePriority(reportId, priority) {
        if (!priority) {
            console.log('Nenhuma prioridade selecionada');
            return;
        }

        console.log(`🔄 Enviando requisição para atualizar prioridade do relatório ${reportId} para: ${priority}`);

        const payload = { priority: priority };
        console.log('Payload:', payload);
        console.log('URL:', `/priority/reports/${reportId}`);
        console.log('CSRF Token:', getCsrfToken().substring(0, 20) + '...');

        fetch(`/priority/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.clone().json().catch(() => response.text());
            console.log(`📨 Resposta recebida - Status: ${response.status}`, data);
            
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${typeof data === 'string' ? data : JSON.stringify(data)}`);
            }
            
            console.log(`✅ Prioridade atualizada com sucesso!`);
            alert(`✅ Prioridade atualizada para ${priority} com sucesso!`);
            location.reload();
        })
        .catch(error => {
            console.error('❌ Erro na requisição:', error);
            alert(`❌ Erro ao atualizar prioridade:\n${error.message}`);
        });
    }

    function updateStatus(reportId, status, userType = 'admin') {
        if (!status) {
            console.log('Nenhum status selecionado');
            return;
        }

        console.log(`🔄 Enviando requisição para atualizar status do relatório ${reportId} para: ${status}`);

        // Determina a URL baseado no tipo de usuário
        const statusUrl = userType === 'secretary' 
            ? `/secretary/reports/${reportId}/status` 
            : `/admin/reports/${reportId}/status`;

        const payload = { status: status };
        console.log('Payload:', payload);
        console.log('URL:', statusUrl);
        console.log('User Type:', userType);
        console.log('CSRF Token:', getCsrfToken().substring(0, 20) + '...');

        fetch(statusUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.clone().json().catch(() => response.text());
            console.log(`📨 Resposta recebida - Status: ${response.status}`, data);
            
            if (!response.ok) {
                throw new Error(`Erro ${response.status}: ${typeof data === 'string' ? data : JSON.stringify(data)}`);
            }
            
            console.log(`✅ Status atualizado com sucesso!`);
            alert(`✅ Status atualizado para ${status} com sucesso!`);
            location.reload();
        })
        .catch(error => {
            console.error('❌ Erro na requisição:', error);
            alert(`❌ Erro ao atualizar status:\n${error.message}`);
        });
    }
</script>
@endpush
