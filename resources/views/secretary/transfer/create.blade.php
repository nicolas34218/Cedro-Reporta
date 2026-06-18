@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-transfer.css') }}">
@endpush

@extends('layouts.secretary', ['active' => 'transfer'])

@section('title', 'Transferir Denúncia')

@section('content')

<div class="transfer-page">

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="transfer-header">
        <h1>Transferência de Denúncias</h1>
        <p>
            Caso a ocorrência não seja de competência desta secretaria,
            selecione o órgão responsável e informe a justificativa da transferência.
        </p>
        <div class="transfer-warning">
            Esta ação encaminha a denúncia para avaliação da secretaria de destino.
            A responsabilidade só passa para ela após o aceite.
        </div>
    </div>

    <div class="transfer-report-card">
        <h3>Ocorrência selecionada para transferência</h3>
        <div class="report-preview">
            <h4>#{{ $report->id }} — {{ $report->title }}</h4>
            <p>{{ Str::limit($report->description, 200) }}</p>
            <small>
                📍 {{ $report->location ?? 'Local não informado' }}
                •
                {{ $report->created_at->format('d/m/Y') }}
            </small>
        </div>
    </div>

    <form method="POST" action="{{ route('secretary.transfer.store', $report) }}" class="transfer-form">
        @csrf

        <div class="form-group">
            <label for="to_secretary_id">
                <i class="fas fa-building"></i>
                Secretaria de Destino
            </label>
            <select id="to_secretary_id" name="to_secretary_id" required>
                <option value="">Selecione uma secretaria</option>
                @foreach ($destinationSecretaries as $destination)
                    <option value="{{ $destination->id }}" {{ old('to_secretary_id') == $destination->id ? 'selected' : '' }}>
                        {{ $destination->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="justification">
                <i class="fas fa-file-alt"></i>
                Justificativa da Transferência *
            </label>
            <textarea
                id="justification"
                name="justification"
                rows="5"
                placeholder="Informe o motivo da transferência"
                required>{{ old('justification') }}</textarea>
        </div>

        <div class="transfer-actions">
            <button type="submit" class="btn-transfer">
                Transferir Denúncia
            </button>

            <a href="{{ route('secretary.transfer.index') }}" class="btn-cancel" style="text-decoration:none; display:inline-block;">
                Cancelar
            </a>
        </div>
    </form>

    <div class="transfer-history">
        <h3>Histórico</h3>

        @if ($history->isEmpty())
            <div class="no-reports-message">
                Esta denúncia ainda não foi transferida.
            </div>
        @else
            @foreach ($history as $transfer)
                <div class="history-card">
                    Transferida de <strong>{{ $transfer->fromSecretary->name }}</strong>
                    para <strong>{{ $transfer->toSecretary->name }}</strong>
                    — <span class="transfer-status-{{ strtolower($transfer->status) }}">{{ $transfer->status }}</span>
                    <br>
                    Motivo: {{ $transfer->justification }}
                    @if ($transfer->rejection_reason)
                        <br>Motivo da rejeição: {{ $transfer->rejection_reason }}
                    @endif
                    <br>
                    <small>{{ $transfer->created_at->format('d/m/Y \à\s H:i') }}</small>
                </div>
            @endforeach
        @endif
    </div>

</div>

@endsection
