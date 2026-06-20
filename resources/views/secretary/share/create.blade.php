@extends('layouts.secretary', ['active' => 'share'])

@section('title', 'Compartilhar Denúncia')

@section('content')

<h1>Compartilhar Denúncia</h1>

<p>Denúncia #{{ $report->id }}</p>

@endsection