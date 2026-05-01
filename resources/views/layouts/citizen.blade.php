<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'CedroReporta')</title>
    <link rel="stylesheet" href="{{ asset('css/citizen-home.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="citizen-header">
        <div class="container-header">
            <div class="brand">
                <span class="logo">⚠</span>
                <span class="brand-name">Cedro<span>Reporta</span></span>
            </div>
            <div class="user-area">
                <span class="user-name">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>

    <!-- botão de logout reutilizável -->
    <x-logout-button />

</body>
</html>