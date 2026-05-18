@extends('layouts.admin', ['active' => 'classify'])

@section('title', 'Classificar Denúncia')
@section('page-title', 'Classificação de Denúncias')
@section('page-subtitle', 'Define a prioridade das denúncias conforme a gravidade de cada ocorrência')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/priority-edit.css') }}">
@endpush

@section('content')
    <!-- Conteúdo será adicionado aqui -->
@endsection
