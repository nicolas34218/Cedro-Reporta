@extends('layouts.secretary', ['active' => 'share'])

@section('title', 'Compartilhar Denúncias')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-share.css') }}">
@endpush

@section('content')

<div class="share-page">

    <div class="share-header">
        <h1>Compartilhamento de Denúncias</h1>

        <p>
            Compartilhe denúncias com outras secretarias quando a resolução
            da ocorrência exigir atuação conjunta entre setores.
        </p>
    </div>

    <div class="reports-grid">

        @forelse($reports as $report)

            <div class="report-card">

                <div class="report-card-header">

                    <span class="report-id">
                        #{{ $report->id }}
                    </span>

                    <span class="status-badge">
                        {{ strtoupper($report->status) }}
                    </span>

                </div>

                <h3>
                    {{ Str::limit($report->title, 70) }}
                </h3>

                <div class="report-meta">
                    <p>
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $report->address }}
                    </p>

                    <p>
                        <i class="fas fa-calendar"></i>
                        {{ $report->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <a href="{{ route('secretary.share.create', $report) }}"
                    class="btn-share">
                        <i class="fas fa-share-alt"></i>
                        Compartilhar
                    </a>
                </div>
                
            </div>

        @empty

            <div class="no-reports-message">
                Nenhuma denúncia disponível para compartilhamento.
            </div>

        @endforelse

    </div>

</div>

@endsection