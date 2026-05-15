<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Cedro Reporta')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <header class="admin-header">
        <div class="admin-header-left">
            <div class="admin-header-logo">
                <span class="admin-header-icon">⚠</span>
                <span class="admin-header-title">Cedro<strong>Reporta</strong></span>
            </div>
        </div>
        <div class="admin-header-right">
            <span class="admin-header-user">{{ auth()->user()->name }}</span>
        </div>
    </header>

    <div class="admin-layout">
        <x-admin.sidebar :active="$active ?? 'dashboard'" />

        <main class="admin-main">
            <header class="admin-topbar">
                <div>
                    <h2>@yield('page-title', 'Administração')</h2>
                    <p>@yield('page-subtitle', 'Painel do administrador')</p>
                </div>
            </header>

            <section class="admin-content">
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>