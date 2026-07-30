<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Cedro Reporta')</title>
    <link rel="stylesheet" href="{{ asset('css/secretary/reports.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div class="secretary-layout">
        @php
            // Verifica se estamos em rotas de compartilhamento ou transferência
            $isShareOrTransfer = request()->routeIs('secretary.share*') || request()->routeIs('secretary.transfers*');
            
            // Se estivermos nessas telas, forçamos o contador de pendências a ser 0
            $effectivePendingCount = $isShareOrTransfer ? 0 : ($pendingCount ?? 0);
        @endphp

        <x-secretary.sidebar
            :active="$active ?? 'dashboard'"
            :pendingCount="$effectivePendingCount" />

        <div class="secretary-main-wrapper">
            <header class="secretary-header">
                <div class="secretary-header-left">
                    @if(Auth::guard('secretary')->check())
                        <div class="secretary-badge">Painel da Secretaria</div>
                    @else
                        <div class="secretary-badge">Painel Administrativo</div>
                    @endif
                </div>
                <div class="secretary-header-right">
                    <div class="user-profile">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                        </div>
                        <span class="user-name">{{ auth()->user()?->name ?? 'Usuário' }}</span>
                    </div>
                </div>
            </header>

            <main class="secretary-main">
                <section class="secretary-content">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>