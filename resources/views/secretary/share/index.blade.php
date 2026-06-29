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
                1 pendente
            </span>

        </div>

        <div class="incoming-card">

            <div class="incoming-status">

                <span class="pending-badge">
                    ⏳ Aguardando resposta
                </span>

            </div>

            <h3>
                Buraco na Rua José de Alencar
            </h3>

            <div class="incoming-info">

                <p>
                    <strong>Secretaria:</strong>
                    Secretaria de Obras
                </p>

                <p>
                    <strong>Data:</strong>
                    21/06/2026 às 14:35
                </p>

            </div>

            <div class="incoming-message">

                Necessário apoio desta secretaria para conclusão da ocorrência.

            </div>

            <div class="incoming-actions">

                <button class="btn-accept">

                    ✔ Aceitar

                </button>

                <button class="btn-reject">

                    ✖ Recusar

                </button>

            </div>

            <div class="reject-form" id="reject-form">

                <label for="reject-reason">
                    Justificativa da recusa
                </label>

                <textarea
                    id="reject-reason"
                    rows="4"
                    placeholder="Explique o motivo pelo qual sua secretaria não pode assumir esta denúncia..."></textarea>

                <div class="reject-actions">

                    <button class="btn-cancel-reject">
                        Cancelar
                    </button>

                    <button class="btn-confirm-reject">
                        Confirmar Recusa
                    </button>

                </div>

            </div>

            <div class="share-result" id="share-result"></div>

        </div>

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

document.addEventListener('DOMContentLoaded', () => {

    const rejectButton = document.querySelector('.btn-reject');
    const acceptButton = document.querySelector('.btn-accept');

    const cancelButton = document.querySelector('.btn-cancel-reject');
    const confirmReject = document.querySelector('.btn-confirm-reject');

    const rejectForm = document.getElementById('reject-form');
    const resultBox = document.getElementById('share-result');
    const reasonField = document.getElementById('reject-reason');

    // Abrir formulário
    rejectButton.addEventListener('click', () => {

        rejectForm.classList.add('active');

    });

    // Cancelar
    cancelButton.addEventListener('click', () => {

        rejectForm.classList.remove('active');

    });

    // Aceitar compartilhamento
    acceptButton.addEventListener('click', () => {

        document.querySelector('.incoming-actions').style.display = 'none';

        rejectForm.style.display = 'none';

        resultBox.className = 'share-result success';

        resultBox.innerHTML = `
            <h4>✔ Compartilhamento aceito</h4>

            <p>
                Sua secretaria agora faz parte da resolução desta denúncia.
            </p>

            <p>
                Você poderá registrar atualizações normalmente.
            </p>
        `;

    });

    // Confirmar recusa
    confirmReject.addEventListener('click', () => {

        const reason = reasonField.value.trim();

        if(reason === ''){

            alert('Informe uma justificativa.');

            return;

        }

        document.querySelector('.incoming-actions').style.display = 'none';

        rejectForm.style.display = 'none';

        resultBox.className = 'share-result error';

        resultBox.innerHTML = `
            <h4>✖ Compartilhamento recusado</h4>

            <p><strong>Justificativa:</strong></p>

            <p>${reason}</p>
        `;

    });

});

</script>

@endsection