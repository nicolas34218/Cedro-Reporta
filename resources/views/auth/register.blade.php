<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="register-page">
        <section class="register-card">
            <header class="register-card-header">
                <h1>Cedro<span>Reporta</span> — Criar conta</h1>
                <p>Preencha os dados para se cadastrar</p>
            </header>

            <form class="register-form" action="{{ route('register.store') }}" method="post">
                @csrf

                <x-form-input name="name" label="NOME COMPLETO" type="text" />
                <x-form-input name="email" label="E-MAIL" type="email" />
                <x-form-input name="password" label="SENHA" type="password" />
                <x-form-input name="password_confirmation" label="CONFIRMAR SENHA" type="password" />

                <button type="submit" class="register-btn">Cadastrar</button>

                <p class="register-login-text">
                    Já tem uma conta? <a href="{{ route('login') }}">Fazer Login</a>
                </p>
            </form>
        </section>
    </main>
</body>
</html>