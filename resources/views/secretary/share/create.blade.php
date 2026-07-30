@extends('layouts.secretary', ['active' => 'share'])

@section('title', $report->shares->isNotEmpty() ? 'Gerenciar Atualizações e Compartilhamento' : 'Compartilhar Denúncia')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/secretary/secretary-share-create.css') }}">
@endpush

@section('content')

<div class="share-create-page">

    <div class="page-header">
        <h1>
            @if($report->shares->isNotEmpty())
                Gerenciar Denúncia (#{{ $report->id }})
            @else
                Compartilhamento de Denúncia
            @endif
        </h1>

        <p>
            @if($report->shares->isEmpty())
                <div class="bloco-explicativo-compartilhamento">
                    <h2>Compartilhamento de Denúncia</h2>
                    <p>Compartilhe esta denúncia com outra secretaria quando a resolução da ocorrência exigir atuação conjunta entre setores.</p>
                </div>
            @else
                <div class="bloco-explicativo-compartilhamento">
                    <h2>Painel de Atualizações</h2>
                    <p>Esta denúncia já possui movimentações. Utilize este painel para postar novas atualizações de andamento ou gerenciar o compartilhamento.</p>
                </div>
            @endif
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:20px; padding:12px 16px; border-radius:12px; background:#e8f7ee; color:#14532d; border:1px solid #86efac;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" style="margin-bottom:20px; padding:12px 16px; border-radius:12px; background:#fdecec; color:#991b1b; border:1px solid #fca5a5;">
            {{ session('error') }}
        </div>
    @endif

    <div class="report-preview-card">

        <h4>Ocorrência selecionada</h4>

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

    @if($isOwner)

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

    @else

        <div class="share-form" style="color:#475569;">
            <i class="fas fa-circle-info"></i>
            Esta denúncia foi compartilhada com a sua secretaria. Você pode acompanhar e
            postar atualizações sobre o andamento abaixo, mas apenas a secretaria
            responsável atual pode encaminhá-la para outro setor.
        </div>

    @endif

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

    {{-- SÓ APARECE SE A DENÚNCIA JÁ FOI COMPARTILHADA --}}
    @if($report->shares->isNotEmpty())
        <div class="share-history">

            <h3>
                <i class="fas fa-comment-dots"></i>
                Atualizações sobre a Denúncia
            </h3>

            <form action="{{ route('secretary.reports.updates.store', $report) }}" method="POST" class="share-form" style="margin-bottom:25px;">
                @csrf

                <div class="form-group">
                    <label>O que está sendo feito em relação a esta denúncia?</label>
                    <textarea
                        name="content"
                        rows="3"
                        placeholder="Descreva a atualização sobre o andamento desta denúncia...">{{ old('content') }}</textarea>
                    @error('content')
                        <small style="color:#b91c1c;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-share">
                        <i class="fas fa-paper-plane"></i>
                        Postar Atualização
                    </button>
                </div>
            </form>

            @forelse($historyEntries as $entry)
                <div class="history-item">
                    <strong>{{ $entry->action }}</strong>
                    <p>
                        {{-- Exibe o nome da secretaria/autor e mais contexto --}}
                        <i class="fas fa-user-shield"></i> {{ $entry->actor_name }} 
                        @if(!empty($entry->actor_role))
                            <span>({{ $entry->actor_role }})</span>
                        @endif
                        &middot;
                        <i class="far fa-clock"></i> {{ $entry->created_at->format('d/m/Y \à\s H:i') }}
                    </p>
                    @if($entry->description)
                        <p style="margin-top: 8px; color: #334155;">{{ $entry->description }}</p>
                    @endif
                </div>
            @empty
                <p>Nenhuma atualização registrada até o momento.</p>
            @endforelse

        </div>
    @endif

</div>

@endsection