@extends('layouts.citizen')

@section('title', 'Detalhes da Denúncia')

@section('content')
<section class="report-detail-page">
    <div class="report-detail-topbar">
        <a href="{{ route('citizen.reports.index') }}" class="btn-back">← Voltar</a>
        <h1>Denúncia #{{ $report->id }}</h1>
        <a href="{{ route('citizen.reports.track-status', $report->id) }}" class="btn-track">Acompanhar Status</a>
    </div>

    <div class="report-detail-container">
        <!-- Informações Principais -->
        <div class="report-section">
            <h2>Informações da Denúncia</h2>

            <div class="report-info-grid">
                <div class="info-item">
                    <label>Título:</label>
                    <p>{{ $report->title }}</p>
                </div>

                <div class="info-item">
                    <label>Status:</label>
                    <p>
                        @php
                            $statusConfig = App\Enums\ReportStatus::getStatusConfig($report->status);
                        @endphp
                        <span class="status-badge" style="display: inline-block; padding: 6px 12px; border-radius: 4px; font-weight: bold; background-color: {{ $statusConfig['color'] }}; color: white;">
                            {{ $statusConfig['icon'] }} {{ $report->status }}
                        </span>
                    </p>
                </div>

                <div class="info-item">
                    <label>Categoria:</label>
                    <p>{{ $report->category }}</p>
                </div>

                <div class="info-item">
                    <label>Data de Registro:</label>
                    <p>{{ $report->created_at->format('d/m/Y') }} às {{ $report->created_at->format('H:i') }}</p>
                </div>

                <div class="info-item">
                    <label>Última Atualização:</label>
                    <p>{{ $report->updated_at->format('d/m/Y') }} às {{ $report->updated_at->format('H:i') }}</p>
                </div>

                <div class="info-item">
                    <label>Localização:</label>
                    <p>{{ $report->location ?? 'Não informada' }}</p>
                </div>
            </div>
        </div>

        <!-- Descrição -->
        <div class="report-section">
            <h2>Descrição</h2>
            <div class="description-box">
                <p>{{ $report->description }}</p>
            </div>
        </div>

        <!-- Ações -->
        <div class="report-actions-footer">
            <a href="{{ route('citizen.reports.index') }}" class="btn-secondary">Voltar para Lista</a>
            <a href="{{ route('citizen.reports.track-status', $report->id) }}" class="btn-primary">Acompanhar Status</a>
        </div>
    </div>
</section>

<style>
    .report-detail-page {
        padding: 20px;
    }

    .report-detail-topbar {
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

    .btn-track {
        background-color: #28A745;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
    }

    .btn-track:hover {
        background-color: #218838;
    }

    .report-detail-container {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .report-section {
        margin-bottom: 30px;
    }

    .report-section h2 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #F0F0F0;
    }

    .report-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .info-item {
        padding: 10px;
        background-color: #F9F9F9;
        border-radius: 4px;
    }

    .info-item label {
        display: block;
        font-weight: 600;
        color: #666;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-item p {
        margin: 0;
        color: #333;
        font-size: 14px;
    }

    .description-box {
        background-color: #F9F9F9;
        padding: 15px;
        border-radius: 4px;
        border-left: 4px solid #007BFF;
        line-height: 1.6;
        color: #555;
    }

    .report-actions-footer {
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #F0F0F0;
    }

    .btn-primary, .btn-secondary {
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        border: none;
        flex: 1;
    }

    .btn-primary {
        background-color: #007BFF;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056B3;
    }

    .btn-secondary {
        background-color: #F0F0F0;
        color: #333;
        border: 1px solid #DDD;
    }

    .btn-secondary:hover {
        background-color: #E0E0E0;
    }

    .status-badge {
        text-transform: uppercase;
        font-size: 12px;
    }
</style>
@endsection
