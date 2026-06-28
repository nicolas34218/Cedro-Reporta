<!-- Tela Acompanhar Status da Denúncia -->
@extends('layouts.citizen')

@section('title', 'Acompanhar Status da Denúncia')

@section('content')
<section class="report-track-page">
    <div class="report-track-topbar">
        <a href="{{ route('citizen.reports.index') }}" class="btn-back">← Voltar</a>
        <div class="topbar-title">
            <h1 class="page-title">Acompanhamento de Status</h1>
            <small style="display:block; color:#666; margin-top:6px;">{{ $report->title }}</small>
        </div>
    </div>

    <div class="report-track-container">
        <!-- Status Atual em Destaque -->
        <div class="status-highlight">
            <div class="status-title">Status Atual</div>

            @php
                $config = App\Enums\ReportStatus::getStatusConfig($report->status);
            @endphp

            <div class="status-display" style="background-color: {{ $config['color'] }}; color: white;">
                <span class="status-icon">{{ $config['icon'] }}</span>
                <span class="status-text">{{ $report->status }}</span>
            </div>

            <p class="status-description">{{ $config['description'] }}</p>
        </div>

        <!-- Informações da Denúncia -->
        <div class="report-info-section">
            <h2>Informações da Denúncia</h2>

            <div class="info-row">
                <div class="info-col">
                    <label>Título:</label>
                    <span>{{ $report->title }}</span>
                </div>
                <div class="info-col">
                    <label>Categoria:</label>
                    <span>{{ $report->category }}</span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-col">
                    <label>Data de Registro:</label>
                    <span>{{ $report->created_at->format('d/m/Y') }} às {{ $report->created_at->format('H:i') }}</span>
                </div>
                <div class="info-col">
                    <label>Última Atualização:</label>
                    <span>{{ $report->updated_at->format('d/m/Y') }} às {{ $report->updated_at->format('H:i') }}</span>
                </div>
            </div>

            @if($report->location)
                <div class="info-row">
                    <div class="info-col full">
                        <label>Localização:</label>
                        <span>📍 {{ $report->location }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Timeline de Status -->
        <div class="timeline-section">
            <h2>Histórico de Atualizações</h2>

            @php
                $historyEntries = $report->histories->sortBy('id');
            @endphp

            <div class="timeline">
                @forelse ($historyEntries as $entry)
                    <div class="timeline-item">
                        <div class="timeline-marker" style="background-color: #2f6b3f;">●</div>
                        <div class="timeline-content">
                            <h4>{{ $entry->action }}</h4>
                            <p>
                                {{ $entry->created_at->format('d/m/Y') }} às {{ $entry->created_at->format('H:i') }}
                                &middot; {{ $entry->actor_name }} ({{ $entry->actor_role }})
                            </p>
                            @if($entry->description)
                                <p>{{ $entry->description }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>Nenhuma atualização registrada até o momento.</p>
                @endforelse
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="action-buttons">
            <a href="{{ route('citizen.reports.show', $report->id) }}" class="btn-secondary">Ver Detalhes Completos</a>
            <a href="{{ route('citizen.reports.index') }}" class="btn-secondary">Voltar para Lista</a>
        </div>
    </div>
</section>

<style>
    .report-track-page {
        padding: 20px;
    }

    .report-track-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #E0E0E0;
        gap: 12px;
    }


    .btn-back {
        color: #333;
        text-decoration: none;
        font-weight: 600;
        background-color: #F0F0F0;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #DDD;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
    }

    .btn-back:hover {
        background-color: #EDEDED;
    }

    .page-title {
        font-size: 20px;
        margin: 0;
        font-weight: 700;
        color: #222;
    }

    .topbar-title { margin-left: 10px; }

    .btn-details {
        background-color: #6C757D;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-details:hover {
        background-color: #5A6268;
    }

    .report-track-container {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .status-highlight {
        background: linear-gradient(135deg, #F5F7FA 0%, #E9ECEF 100%);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        text-align: center;
        border: 2px solid #E0E0E0;
    }

    .status-title {
        font-size: 12px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .status-display {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 30px;
        border-radius: 6px;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .status-icon {
        font-size: 32px;
    }

    .status-text {
        font-size: 20px;
    }

    .status-description {
        color: #666;
        font-size: 14px;
        margin: 10px 0 0 0;
    }

    .report-info-section {
        margin-bottom: 30px;
    }

    .report-info-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F0F0F0;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 10px;
    }

    .info-col {
        display: flex;
        flex-direction: column;
    }

    .info-col.full {
        grid-column: 1 / -1;
    }

    .info-col label {
        font-weight: 600;
        color: #999;
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-col span {
        color: #333;
        font-size: 14px;
    }

    .timeline-section {
        margin-bottom: 30px;
    }

    .timeline-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F0F0F0;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #E0E0E0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 20px;
        position: relative;
        border-left: 3px solid #E0E0E0;
        padding-left: 50px;
    }

    .timeline-marker {
        position: absolute;
        left: -12px;
        top: 5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    .timeline-content h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .timeline-content p {
        margin: 0;
        font-size: 12px;
        color: #999;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #F0F0F0;
        justify-content: flex-end;
        align-items: center;
    }

    .btn-secondary {
        /* consistent, non-wrapping buttons */
        flex: 0 0 auto;
        padding: 8px 16px;
        min-width: 150px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        background-color: #F0F0F0;
        color: #333;
        border: 1px solid #DDD;
        font-size: 13px;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .action-buttons { flex-direction: column; align-items: stretch; }
        .btn-secondary { width: 100%; min-width: 0; }
    }

    .btn-secondary:hover {
        background-color: #E0E0E0;
    }
</style>
@endsection
