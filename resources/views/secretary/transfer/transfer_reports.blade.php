@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-transfer.css') }}">
@endpush

@extends('layouts.secretary', ['active' => 'transfer'])

@section('title', 'Transferir Denúncias')

@section('content')

<div class="transfer-page">

    <div class="transfer-header">

        <h1>Transferência de Denúncias</h1>

        <p>
            Caso a ocorrência não seja de competência desta secretaria,
            selecione o órgão responsável e informe a justificativa da transferência.
        </p>

        <div class="transfer-warning">
            Esta ação altera a secretaria responsável pela denúncia.
        </div>

    </div>

    <div class="transfer-report-card">

        <h3>Ocorrência selecionada para transferência</h3>

        <div class="report-preview">

            <h4>Buraco grande na Av. Brasil próximo à escola</h4>

            <p>
                Tem um buraco bem grande aqui na via que está ficando perigoso...
            </p>

            <small>
                📍 Bairro Centro
                •
                05/04/2026
            </small>

        </div>

    </div>

    <div class="transfer-form">

        <div class="form-group">

            <label>
                <i class="fas fa-building"></i>
                Secretaria de Destino
            </label>

            <select>

                <option>
                    Selecione uma secretaria
                </option>

            </select>

        </div>

        <div class="form-group">

            <label>
                <i class="fas fa-file-alt"></i>
                Justificativa da Transferência *
            </label>

            <textarea
                rows="5"
                placeholder="Informe o motivo da transferência"
                required>
            </textarea>

        </div>

        <div class="transfer-actions">

            <button class="btn-transfer">
                Transferir Denúncia
            </button>

            <button class="btn-cancel">
                Cancelar
            </button>

        </div>

    </div>

    <div class="transfer-history">

        <h3>Histórico</h3>

        <div class="history-card">

            Transferida de Limpeza Urbana para Infraestrutura
            Motivo: Competência relacionada à manutenção viária.
            05/04/2026 às 10:22

        </div>

    </div>

</div>

@endsection