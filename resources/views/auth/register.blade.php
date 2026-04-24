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

                <label for="name">NOME COMPLETO</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}">
                @error('name')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="email">E-MAIL</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}">
                @error('email')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="password">SENHA</label>
                <input id="password" name="password" type="password">
                @error('password')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <label for="password_confirmation">CONFIRMAR SENHA</label>
                <input id="password_confirmation" name="password_confirmation" type="password">
                @error('password_confirmation')
                    <small class="form-error">{{ $message }}</small>
                @enderror

                <button type="submit" class="register-btn">Cadastrar</button>

                <p class="register-login-text">
                    Já tem uma conta? <a href="{{ route('login') }}">Fazer Login</a>
                </p>
            </form>
        </section>
    </main>
</body>
</html>