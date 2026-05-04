@extends('layouts.citizen')

@section('title', 'Visualizar Denúncias')

@section('content')
<h1>Minhas Denúncias</h1>

@if(session('success'))
    <p style="color: green; border: 1px solid green; padding: 10px; background-color: #f0f0f0;">
        ✓ {{ session('success') }}
    </p>
@endif

@if(session('error'))
    <p style="color: red; border: 1px solid red; padding: 10px; background-color: #f0f0f0;">
        ✗ {{ session('error') }}
    </p>
@endif

<p>Total: <strong>{{ $reports->total() }}</strong> denúncia(s)</p>

@if($reports->count() > 0)
    @foreach($reports as $report)
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px;">
            <h3>#{{ $report->id }} - {{ $report->title }}</h3>
            
            <p><strong>Descrição:</strong> {{ $report->description }}</p>
            
            <p><strong>Categoria:</strong> {{ $report->category }}</p>
            
            <p><strong>Status:</strong> {{ $report->status }}</p>
            
            @if($report->location)
                <p><strong>Localização:</strong> {{ $report->location }}</p>
            @endif
            
            <p><strong>Data:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</p>
            
            <p>
                <a href="{{ route('citizen.reports.show', $report->id) }}">Ver Detalhes</a> | 
                <a href="{{ route('citizen.reports.track-status', $report->id) }}">Acompanhar Status</a>
            </p>
        </div>
    @endforeach

    @if($reports->hasPages())
        <div style="margin-top: 20px;">
            {{ $reports->links() }}
        </div>
    @endif
@else
    <p>Você ainda não tem denúncias registradas. <a href="{{ route('citizen.reports.create') }}">Clique aqui para registrar uma.</a></p>
@endif

<p style="margin-top: 20px;">
    <a href="{{ route('citizen.reports.create') }}">Criar Nova Denúncia</a> | 
    <a href="{{ route('citizen.home') }}">Voltar para Home</a>
</p>
@endsection