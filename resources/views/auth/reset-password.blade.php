<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <main class="register-page">
        <section class="register-card">
            <header class="register-card-header">
                <h1>Cedro<span>Reporta</span> — Redefinir Senha</h1>
                <p>Informe seu e-mail, a senha atual e a nova senha</p>
            </header>

            <form class="register-form" action="{{ route('password.update') }}" method="post">
                @csrf

                <x-form-input name="email" label="E-MAIL" type="email" />
                <x-form-input name="current_password" label="SENHA ATUAL" type="password" />
                <x-form-input name="password" label="NOVA SENHA" type="password" />
                <x-form-input name="password_confirmation" label="CONFIRMAR NOVA SENHA" type="password" />

                <button type="submit" class="register-btn">Alterar Senha</button>

                <p class="register-login-text">
                    <a href="{{ route('login') }}">Voltar para o login</a>
                </p>
            </form>
        </section>
    </main>
</body>
</html>
