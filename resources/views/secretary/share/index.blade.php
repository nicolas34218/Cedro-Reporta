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

    <div class="incoming-shares">

        <div class="incoming-header">

            <h2>
                <i class="fas fa-inbox"></i>
                Denúncias compartilhadas com você
            </h2>

            <span class="incoming-count">
                {{ $incomingShares->count() }}
                {{ $incomingShares->count() == 1 ? 'pendente' : 'pendentes' }}
            </span>

        </div>

    @forelse($incomingShares as $share)

        <div class="incoming-card">

            <div class="incoming-status">

                <span class="pending-badge">
                    ⏳ Aguardando resposta
                </span>

            </div>

            <h3>
                {{ $share->report->title }}
            </h3>

            <div class="incoming-info">

                <p>
                    <strong>Secretaria:</strong>
                    {{ $share->fromSecretary->name }}
                </p>

                <p>
                    <strong>Data:</strong>
                    {{ $share->shared_at->format('d/m/Y \à\s H:i') }}
                </p>

            </div>

            <div class="incoming-message">

                {{ $share->message ?: 'Sem observações.' }}

            </div>

            <div class="incoming-actions">

                <form
                    action="{{ route('secretary.share.accept', $share) }}"
                    method="POST"
                    style="display:inline;">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn-accept">

                        ✔ Aceitar

                    </button>

                </form>

                <button
                    type="button"
                    class="btn-reject toggle-reject">

                    ✖ Recusar

                </button>

            </div>

            <form
                action="{{ route('secretary.share.reject', $share) }}"
                method="POST"
                class="reject-form"
                style="display:none;">

                @csrf
                @method('PATCH')

                <textarea
                    name="response"
                    rows="4"
                    placeholder="Informe a justificativa da recusa..."
                    required>
                </textarea>

                <button
                    type="submit"
                    class="btn-confirm-reject">

                    Confirmar Recusa

                </button>

            </form>

        </div>

    @empty

        <div class="no-reports-message">

            Nenhuma denúncia compartilhada com sua secretaria.

        </div>

    @endforelse

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

<script>

document.querySelectorAll('.toggle-reject').forEach(button => {

    button.addEventListener('click', function () {

        const form =
            this.closest('.incoming-card')
                .querySelector('.reject-form');

        form.style.display =
            form.style.display === 'block'
                ? 'none'
                : 'block';

    });

});

</script>

@endsection