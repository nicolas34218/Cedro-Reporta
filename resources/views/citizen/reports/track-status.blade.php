<!-- Tela Acompanhar Status da Denúncia -->
@extends('layouts.citizen')

@section('title', 'Acompanhar Status da Denúncia')

@push('styles')
    <link rel="stylesheet"
          href="{{ asset('css/citizen/report-track-status.css') }}">
@endpush

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

            @if($historyEntries->count())

                <div class="timeline">
                        @foreach ($historyEntries as $entry)

                            <div class="timeline-item">
                                <div class="timeline-marker"></div>

                                <div class="timeline-content">
                                    <h4>{{ $entry->action }}</h4>

                                    <p>
                                        {{ $entry->created_at->format('d/m/Y') }}
                                        às
                                        {{ $entry->created_at->format('H:i') }}

                                        &middot;

                                        {{ $entry->actor_name }}
                                        ({{ $entry->actor_role }})
                                    </p>

                                    @if($entry->description)
                                        <p>{{ $entry->description }}</p>
                                    @endif
                                </div>
                            </div>

                        @endforeach
                    </div>

                @else

                    <div class="timeline-empty">
                        <i class="bi bi-clock-history"></i>
                        <p>Nenhuma atualização registrada até o momento.</p>
                    </div>

                @endif
        </div>

        <!-- Botões de Ação -->
        <div class="action-buttons">
            <a href="{{ route('citizen.reports.show', $report->id) }}" class="btn-secondary">Ver Detalhes Completos</a>
            <a href="{{ route('citizen.reports.index') }}" class="btn-secondary">Voltar para Lista</a>
        </div>
    </div>
</section>

@endsection
