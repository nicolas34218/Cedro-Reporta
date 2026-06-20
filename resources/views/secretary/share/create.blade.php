@extends('layouts.secretary', ['active' => 'share'])

@section('title', 'Compartilhar Denúncia')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-share-create.css') }}">
@endpush

@section('content')

<div class="share-create-page">

    <div class="page-header">
        <h1>Compartilhamento de Denúncia</h1>

        <p>
            Compartilhe esta denúncia com outra secretaria quando a resolução
            da ocorrência exigir atuação conjunta entre setores.
        </p>
    </div>

    <div class="report-preview-card">

        <h4>Ocorrência selecionada para compartilhamento</h4>

        <div class="report-card">

            <h3>
                {{ $report->title }}
            </h3>

            <p>
                {{ Str::limit($report->description, 250) }}
            </p>

            <small>
                <i class="fas fa-map-marker-alt"></i>

                {{ $report->address ?? 'Endereço não informado' }}
            </small>

        </div>

    </div>

    <form
        action="{{ route('secretary.share.store', $report) }}"
        method="POST"
        class="share-form">

        @csrf

        <div class="form-group">
            <label>
                Compartilhar com
            </label>

            <select name="to_secretary_id" required>

                <option value="">
                    Selecione uma secretaria
                </option>

                @foreach($destinationSecretaries as $secretary)

                    <option value="{{ $secretary->id }}">
                        {{ $secretary->name }}
                    </option>

                @endforeach

            </select>
        </div>

        <div class="form-group">

            <label>
                Observação
            </label>

            <textarea
                name="message"
                rows="4"
                placeholder="Explique por que esta denúncia está sendo compartilhada..."></textarea>

        </div>

        <div class="form-actions">

            <button type="submit" class="btn-share">
                <i class="fas fa-share-alt"></i>
                Compartilhar Denúncia
            </button>

            <a href="{{ route('secretary.share.index') }}"
               class="btn-cancel">
                Cancelar
            </a>

        </div>

    </form>
    @if($history->isNotEmpty())

        <div class="share-history">

            <h3>
                <i class="fas fa-building"></i>
                Secretarias vinculadas
            </h3>

            @foreach($history as $share)

                <div class="history-item">

                    <strong>
                        {{ $share->toSecretary->name }}
                    </strong>

                    <p>
                        {{ $share->message ?: 'Sem observação.' }}
                    </p>

                </div>

            @endforeach

        </div>

@endif

</div>

@endsection