<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tela Principal - Cidadão</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/citizen-home.css') }}">
</head>
<body>
    <main class="home-container">
        <h1>Tela principal</h1>
    </main>

    <form action="{{ route('logout') }}" method="post" class="logout-form">
        @csrf
        <button type="submit" class="logout-btn">Sair</button>
    </form>
</body>
</html>