@extends('layouts.secretary', ['active' => 'dashboard'])

@section('title', 'Detalhes da Denúncia')

@php
    use Illuminate\Support\Str;
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/secretary/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/secretary/report-show.css') }}">
@endpush

@section('content')
<div class="report-show-page">

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px; padding:12px 16px; border-radius:12px; background:#e8f7ee; color:#14532d; border:1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    <div class="report-show-topbar">
        <a href="{{ route('secretary.dashboard') }}" class="btn-back">← Voltar</a>
        <div class="report-show-title">
            <h1>#{{ $report->id }} — {{ $report->title }}</h1>
        </div>
    </div>

    <div class="report-show-section">
        <h2>Informações da Denúncia</h2>
        <div class="report-info-grid">
            <div class="info-item">
                <label>Status</label>
                <div class="status-update-control">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}" id="status-badge">
                        {{ $report->status }}
                    </span>

                    <div class="status-update-form">
                        <select id="status-select" class="status-select" aria-label="Selecionar novo status da denúncia">
                            @foreach (\App\Enums\ReportStatus::getAll() as $statusOption)
                                <option value="{{ $statusOption }}" {{ $report->status === $statusOption ? 'selected' : '' }}>
                                    {{ $statusOption }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="status-update-btn" class="btn-update-status">
                            Atualizar Status
                        </button>
                    </div>

                    <p id="status-update-message" class="status-update-message"></p>
                </div>
            </div>

            <div class="info-item">
                <label>Prioridade</label>
                <p>
                    @if($report->priority)
                        <span class="priority-badge priority-{{ strtolower($report->priority) }}">{{ $report->priority }}</span>
                    @else
                        <span class="priority-badge priority-unclassified">Não classificada</span>
                    @endif
                </p>
            </div>

            <div class="info-item">
                <label>Categoria</label>
                <p>{{ $report->category }}</p>
            </div>

            <div class="info-item">
                <label>Secretaria Responsável</label>
                <p>{{ $report->secretary->name ?? 'Não atribuída' }}</p>
            </div>

            <div class="info-item">
                <label>Denunciante</label>
                <p>
                    @if($report->is_anonymous)
                        Anônimo
                    @elseif($report->citizen)
                        {{ $report->citizen->name }}
                    @else
                        Desconhecido
                    @endif
                </p>
            </div>

            <div class="info-item">
                <label>Localização</label>
                <p>{{ $report->location ?? 'Não informada' }}</p>
            </div>

            <div class="info-item">
                <label>Data de Registro</label>
                <p>{{ $report->created_at->format('d/m/Y') }} às {{ $report->created_at->format('H:i') }}</p>
            </div>

            <div class="info-item">
                <label>Última Atualização</label>
                <p>{{ $report->updated_at->format('d/m/Y') }} às {{ $report->updated_at->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="report-show-section">
        <h2>Descrição</h2>
        <div class="description-box">
            {{ $report->description }}
        </div>
    </div>

    {{-- SEÇÃO 1: FEEDBACKS E ATUALIZAÇÕES DA DENÚNCIA --}}
    <div class="report-show-section">
        <h2>Feedbacks e Atualizações</h2>

        @if ($report->histories->isEmpty())
            <div class="no-history-message">
                Nenhum feedback ou atualização registrado até o momento.
            </div>
        @else
            <div class="history-timeline">
                @foreach ($report->histories->sortByDesc('created_at') as $feedback)
                    <div class="history-entry">
                        <span class="history-entry-marker"></span>
                        <div class="history-entry-action">{{ $feedback->action ?? 'Atualização' }}</div>
                        <div class="history-entry-meta">
                            {{ $feedback->actor_name }} ({{ $feedback->actor_role }})
                            &middot;
                            {{ $feedback->created_at->format('d/m/Y \à\s H:i') }}
                        </div>
                        <div class="history-entry-description">
                            {{ $feedback->description }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- SEÇÃO 2: HISTÓRICO ADMINISTRATIVO DE MOVIMENTAÇÕES --}}
    <div class="report-show-section">
        <h2>Histórico de Movimentações</h2>

        @php
            $movements = $report->shares->concat($report->transfers)->sortByDesc('created_at');
        @endphp

        @if ($movements->isEmpty())
            <div class="no-history-message">
                Nenhuma movimentação administrativa registrada.
            </div>
        @else
            <div class="history-timeline">
                @foreach ($movements as $entry)
                    <div class="history-entry">
                        <span class="history-entry-marker"></span>
                        
                        {{-- LAYOUT DE COMPARTILHAMENTO --}}
                        @if (class_basename($entry) === 'ReportShare')
                            <div class="history-entry-action">Compartilhamento</div>
                            <div class="history-entry-meta">
                                De: {{ $entry->fromSecretary->name ?? 'Sistema' }} para {{ $entry->toSecretary->name ?? 'Outra Secretaria' }}
                                &middot; {{ $entry->created_at->format('d/m/Y \à\s H:i') }}
                            </div>
                            <div class="history-entry-description">
                                Status: <strong>{{ ucfirst(__($entry->status)) }}</strong>
                                @if($entry->message)
                                    <br>Observação enviada: {{ $entry->message }}
                                @endif
                                @if($entry->response)
                                    <br>Resposta recebida: {{ $entry->response }}
                                @endif
                            </div>

                        {{-- LAYOUT DE TRANSFERÊNCIA --}}
                        @elseif (class_basename($entry) === 'ReportTransfer')
                            <div class="history-entry-action">Transferência</div>
                            <div class="history-entry-meta">
                                Alteração de Responsabilidade &middot; {{ $entry->created_at->format('d/m/Y \à\s H:i') }}
                            </div>
                            <div class="history-entry-description">
                                A denúncia foi transferida definitivamente.
                            </div>
                        @endif
                        
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    (function () {
        const statusUrl = @json(route('secretary.report.status', $report));
        const select = document.getElementById('status-select');
        const badge = document.getElementById('status-badge');
        const button = document.getElementById('status-update-btn');
        const message = document.getElementById('status-update-message');

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content;
        }

        function slugifyStatus(status) {
            return status.toLowerCase().replace(/ /g, '-');
        }

        function showMessage(text, type) {
            message.textContent = text;
            message.className = 'status-update-message status-update-message-' + type;
        }

        button.addEventListener('click', function () {
            const currentStatus = badge.textContent.trim();
            const newStatus = select.value;

            if (newStatus === currentStatus) {
                showMessage('A denúncia já está com o status "' + newStatus + '".', 'info');
                return;
            }

            const confirmed = window.confirm(
                'Tem certeza que deseja alterar o status da denúncia de "' + currentStatus + '" para "' + newStatus + '"?'
            );

            if (!confirmed) {
                select.value = currentStatus;
                return;
            }

            button.disabled = true;
            select.disabled = true;
            const originalLabel = button.textContent;
            button.textContent = 'Atualizando...';

            fetch(statusUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ status: newStatus }),
            })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Erro ao atualizar status. Tente novamente.');
                    }
                    return data;
                })
                .then((data) => {
                    badge.textContent = data.status;
                    badge.className = 'status-badge status-' + slugifyStatus(data.status);
                    select.value = data.status;
                    showMessage(data.message || 'Status atualizado com sucesso!', 'success');
                })
                .catch((error) => {
                    select.value = currentStatus;
                    showMessage(error.message, 'error');
                })
                .finally(() => {
                    button.disabled = false;
                    select.disabled = false;
                    button.textContent = originalLabel;
                });
        });
    })();
</script>
@endpush