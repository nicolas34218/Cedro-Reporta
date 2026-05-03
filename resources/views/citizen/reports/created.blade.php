@extends('layouts.citizen')

@section('title', 'Denúncia Criada com Sucesso')

@section('content')
<div style="padding: 20px; font-family: Arial, sans-serif;">
    <h1>Denúncia Registrada com Sucesso!</h1>
    
    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
        Sua denúncia foi criada com sucesso! ID: #{{ $report->id }}
    </div>

    <h2>Detalhes da Denúncia Criada:</h2>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">ID:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->id }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Título:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->title }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Descrição:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->description }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Categoria:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->category }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Status:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->status }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Localização:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->location ?? 'Não informada' }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="border: 1px solid #ddd; padding: 10px; font-weight: bold;">Data de Criação:</td>
            <td style="border: 1px solid #ddd; padding: 10px;">{{ $report->created_at->format('d/m/Y H:i:s') }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <a href="{{ route('citizen.reports.index') }}" style="display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">Ver Lista de Denúncias</a>
        <a href="{{ route('citizen.reports.create') }}" style="display: inline-block; background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Criar Nova Denúncia</a>
        <a href="{{ route('citizen.home') }}" style="display: inline-block; background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Voltar para Home</a>
    </div>
</div>
@endsection
