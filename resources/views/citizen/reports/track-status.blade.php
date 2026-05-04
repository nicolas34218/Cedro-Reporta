@extends('layouts.citizen')

@section('title', 'Acompanhar Status da Denúncia')

@section('content')
<section class="report-track-page">
    <div class="report-track-topbar">
        <a href="{{ route('citizen.reports.index') }}" class="btn-back">← Voltar</a>
        <h1>Acompanhamento de Status - Denúncia #{{ $report->id }}</h1>
        <a href="{{ route('citizen.reports.show', $report->id) }}" class="btn-details">Ver Detalhes</a>
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

            <div class="timeline">
                <div class="timeline-item" style="border-color: {{ App\Enums\ReportStatus::getStatusConfig('Pendente')['color'] }};">
                    <div class="timeline-marker" style="background-color: {{ App\Enums\ReportStatus::getStatusConfig('Pendente')['color'] }};">{{ App\Enums\ReportStatus::getStatusConfig('Pendente')['icon'] }}</div>
                    <div class="timeline-content">
                        <h4>Denúncia Registrada</h4>
                        <p>{{ $report->created_at->format('d/m/Y') }} às {{ $report->created_at->format('H:i') }}</p>
                    </div>
                </div>

                @if($report->status !== 'Pendente')
                    <div class="timeline-item" style="border-color: {{ App\Enums\ReportStatus::getStatusConfig('Em Análise')['color'] }};">
                        <div class="timeline-marker" style="background-color: {{ App\Enums\ReportStatus::getStatusConfig('Em Análise')['color'] }};">{{ App\Enums\ReportStatus::getStatusConfig('Em Análise')['icon'] }}</div>
                        <div class="timeline-content">
                            <h4>Em Análise</h4>
                            <p>{{ $report->updated_at->format('d/m/Y') }} às {{ $report->updated_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if(in_array($report->status, ['Resolvida', 'Fechada']))
                    @php
                        $finalConfig = App\Enums\ReportStatus::getStatusConfig($report->status);
                    @endphp
                    <div class="timeline-item" style="border-color: {{ $finalConfig['color'] }};">
                        <div class="timeline-marker" style="background-color: {{ $finalConfig['color'] }};">{{ $finalConfig['icon'] }}</div>
                        <div class="timeline-content">
                            <h4>{{ $report->status }}</h4>
                            <p>{{ $report->updated_at->format('d/m/Y') }} às {{ $report->updated_at->format('H:i') }}</p>
                        </div>
                    </div>
                @endif
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
    }

    .btn-back {
        color: #007BFF;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-back:hover {
        text-decoration: underline;
    }

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
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #F0F0F0;
    }

    .btn-secondary {
        flex: 1;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        background-color: #F0F0F0;
        color: #333;
        border: 1px solid #DDD;
    }

    .btn-secondary:hover {
        background-color: #E0E0E0;
    }
</style>
@endsection
