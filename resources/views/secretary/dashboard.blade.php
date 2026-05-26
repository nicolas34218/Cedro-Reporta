@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard Secretária')
@section('page-title', 'Dashboard da Secretária')
@section('page-subtitle', 'Visualização das denúncias da sua categoria')

@push('styles')
<link rel="stylesheet" href="/css/secretary/dashboard.css">
<style>
    /* Estilos extras para o select de prioridade na tabela e o novo card */
    .priority-select { padding: 4px 8px; border-radius: 4px; border: 1px solid #ccc; background-color: #fff; cursor: pointer; outline: none; transition: border-color 0.3s; font-size: 0.85rem; }
    .priority-select:focus { border-color: #3b82f6; }
    .stat-card.unclassified { background-color: #fef2f2; border-left: 4px solid #ef4444; }
    .stat-card.unclassified h3 { color: #991b1b; }
    .stat-card.unclassified .stat-number { color: #ef4444; }
</style>
@endpush

@section('content')
    <section class="statistics">
        <div class="stat-card">
            <h3>TOTAL DE DENÚNCIAS</h3>
            <p class="stat-number">{{ $statistics['total_reports'] ?? 0 }}</p>
        </div>

        <div class="stat-card unclassified">
            <h3>SEM CLASSIFICAÇÃO</h3>
            @php
                $topUnclassified = \App\Models\Report::where('secretary_id', auth()->id())->whereNull('priority')->count();
            @endphp
            <p class="stat-number" id="top-unclassified-count">{{ $topUnclassified }}</p>
        </div>

        <div class="stat-card pending">
            <h3>PENDENTES</h3>
            <p class="stat-number">{{ $statistics['pending_reports'] ?? 0 }}</p>
        </div>

        <div class="stat-card in-analysis">
            <h3>EM ANÁLISE</h3>
            <p class="stat-number">{{ $statistics['analyzing_reports'] ?? 0 }}</p>
        </div>

        <div class="stat-card resolved">
            <h3>RESOLVIDAS</h3>
            <p class="stat-number">{{ $statistics['resolved_reports'] ?? 0 }}</p>
        </div>
    </section>

    <section class="reports-section">
        <div class="section-header">
            <h3>Denúncias da categoria: <strong>{{ $category }}</strong></h3>
        </div>

        @if ($reports->isEmpty())
            <div class="no-reports-message">
                <i class="fas fa-inbox"></i>
                <p>Nenhuma denúncia cadastrada para esta categoria.</p>
            </div>
        @else
            <div class="reports-table-wrapper">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Denunciante</th>
                            <th>Status</th>
                            <th>Prioridade Atual</th>
                            <th>Data</th>
                            <th>Ações (Classificar)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td class="id-cell">#{{ $report->id }}</td>
                                <td class="title-cell">{{ Str::limit($report->title, 45) }}</td>
                                <td class="citizen-cell">
                                    @if($report->citizen)
                                        <span title="{{ $report->citizen->email }}">{{ Str::limit($report->citizen->name, 20) }}</span>
                                    @else
                                        <span class="text-muted">Desconhecido</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td>
                                    <span id="priority-label-{{ $report->id }}">
                                        @if($report->priority)
                                            <span class="priority-badge priority-{{ strtolower($report->priority) }}">{{ $report->priority }}</span>
                                        @else
                                            <span class="priority-badge priority-unclassified" style="color: #6b7280;">Não classificada</span>
                                        @endif
                                    </span>
                                </td>
                                <td class="date-cell">{{ $report->created_at->format('d/m/Y H:i') }}</td>

                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
<script>
    // Função para atualizar a Prioridade em tempo real
    function updatePriority(reportId, selectElement) {
        const priority = selectElement.value;
        if (!priority) return;

        fetch(`/priority/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ priority: priority })
        })
        .then(response => {
            if (!response.ok) throw new Error('Erro ao atualizar prioridade');
            
            // VERIFICA: A denúncia NÃO TINHA classificação antes?
            if (selectElement.getAttribute('data-is-classified') === 'false') {
                // Marca que agora está classificada
                selectElement.setAttribute('data-is-classified', 'true');
                
                // 1. Atualiza o Card Superior do Dashboard
                const topBadge = document.getElementById('top-unclassified-count');
                if (topBadge) {
                    let currentTopCount = parseInt(topBadge.innerText) || 0;
                    topBadge.innerText = Math.max(0, currentTopCount - 1);
                }

                // 2. Atualiza a bolinha de Notificação do Menu Lateral (Sidebar)
                const sidebarBadge = document.getElementById('sidebar-unclassified-count');
                if (sidebarBadge) {
                    let currentSideCount = parseInt(sidebarBadge.innerText) || 0;
                    let newCount = Math.max(0, currentSideCount - 1);
                    if (newCount === 0) {
                        sidebarBadge.style.display = 'none'; // Some se chegar a zero
                    } else {
                        sidebarBadge.innerText = newCount;
                    }
                }
            }

            // (Opcional) Atualiza o texto na coluna "Prioridade Atual" para não ter que recarregar a tela
            const labelSpan = document.getElementById(`priority-label-${reportId}`);
            if(labelSpan) {
                // Remove formatações velhas e adiciona a nova
                labelSpan.innerHTML = `<span class="priority-badge priority-${priority.toLowerCase()}">${priority}</span>`;
            }

            // Efeito visual na borda para a secretária saber que funcionou
            const originalBorder = selectElement.style.borderColor;
            selectElement.style.borderColor = '#10b981'; // verde sucesso
            setTimeout(() => { selectElement.style.borderColor = originalBorder; }, 1500);

            console.log(`Sucesso: Prioridade atualizada para ${priority}`);
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Não foi possível salvar a prioridade. Verifique sua conexão e tente novamente.');
        });
    }
</script>
@endpush