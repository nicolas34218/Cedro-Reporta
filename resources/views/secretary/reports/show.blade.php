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
                <p>
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $report->status)) }}">
                        {{ $report->status }}
                    </span>
                </p>
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

    <div class="report-show-section">
        <h2>Histórico de Atualizações</h2>

        @if ($report->histories->isEmpty())
            <div class="no-history-message">
                Nenhuma atualização registrada até o momento.
            </div>
        @else
            <div class="history-timeline">
                @foreach ($report->histories as $entry)
                    <div class="history-entry">
                        <span class="history-entry-marker"></span>
                        <div class="history-entry-action">{{ $entry->action }}</div>
                        <div class="history-entry-meta">
                            {{ $entry->actor_name }} ({{ $entry->actor_role }})
                            &middot;
                            {{ $entry->created_at->format('d/m/Y \à\s H:i') }}
                        </div>
                        @if($entry->description)
                            <div class="history-entry-description">{{ $entry->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
