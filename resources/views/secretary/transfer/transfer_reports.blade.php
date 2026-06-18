@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-transfer.css') }}">
@endpush

@extends('layouts.secretary', ['active' => 'transfer'])

@section('title', 'Transferir Denúncias')

@section('content')

<div class="transfer-page">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="transfer-header">
        <h1>Encaminhamentos Recebidos</h1>
        <p>
            Denúncias que outras secretarias encaminharam para a sua área, aguardando sua avaliação.
        </p>
    </div>

    @if ($incomingTransfers->isEmpty())
        <div class="no-reports-message">
            Nenhum encaminhamento pendente para avaliar.
        </div>
    @else
        @foreach ($incomingTransfers as $transfer)
            <div class="history-card incoming-card">
                <h4>#{{ $transfer->report->id }} — {{ $transfer->report->title }}</h4>
                <p>Origem: <strong>{{ $transfer->fromSecretary->name }}</strong></p>
                <p>Justificativa: {{ $transfer->justification }}</p>
                <small>{{ $transfer->created_at->format('d/m/Y \à\s H:i') }}</small>

                <div class="transfer-decision-actions">
                    <form method="POST" action="{{ route('secretary.transfer.accept', $transfer) }}">
                        @csrf
                        <button type="submit" class="btn-transfer">Aceitar</button>
                    </form>

                    <form method="POST" action="{{ route('secretary.transfer.reject', $transfer) }}" class="reject-form">
                        @csrf
                        <textarea name="rejection_reason" rows="2" placeholder="Motivo da rejeição" required></textarea>
                        <button type="submit" class="btn-cancel">Rejeitar</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <div class="transfer-header" style="margin-top:40px;">
        <h1>Minhas Denúncias</h1>
        <p>
            Caso uma ocorrência não seja de competência desta secretaria, selecione-a para transferir.
        </p>
    </div>

    @if ($transferableReports->isEmpty())
        <div class="no-reports-message">
            Nenhuma denúncia disponível para transferência.
        </div>
    @else
        <div class="reports-table-wrapper">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transferableReports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td>{{ Str::limit($report->title, 45) }}</td>
                            <td>{{ $report->status }}</td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('secretary.transfer.create', $report) }}" class="btn-transfer" style="text-decoration:none; display:inline-block;">
                                    Transferir
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection
