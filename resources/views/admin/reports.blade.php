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
                                <option value="Urgente" {{ $report->priority === 'Urgente' ? 'selected' : '' }}>Urgente</option>
                                <option value="Alta" {{ $report->priority === 'Alta' ? 'selected' : '' }}>Alta</option>
                                <option value="Média" {{ $report->priority === 'Média' ? 'selected' : '' }}>Média</option>
                                <option value="Baixa" {{ $report->priority === 'Baixa' ? 'selected' : '' }}>Baixa</option>
                            </select>

                            <select class="status-select" onchange="updateStatus({{ $report->id }}, this.value)">
                                <option value="">Selecione Status</option>
                                <option value="Aberta" {{ $report->status === 'Aberta' ? 'selected' : '' }}>Aberta</option>
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
    function updatePriority(reportId, priority) {
        if (!priority) {
            return; // Não faz nada se não houver seleção
        }

    function updateStatus(reportId, status) {
    if (!status) {
        return; // Não faz nada se não houver seleção
    }

        // Fazer requisição PUT para atualizar o status
        fetch(`/admin/reports/${reportId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                status: status
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao atualizar status');
            }
            console.log(`Status do relatório ${reportId} atualizado para ${status}`);
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar status. Tente novamente.');
        });
    }    

        // Fazer requisição PUT para atualizar a prioridade
        fetch(`/priority/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                priority: priority
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao atualizar prioridade');
            }
            // Mostrar feedback ao usuário (opcional)
            console.log(`Prioridade do relatório ${reportId} atualizada para ${priority}`);
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar prioridade. Tente novamente.');
        });
    }
</script>
@endpush
