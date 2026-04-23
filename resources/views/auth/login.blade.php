<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left">
            <div class="left-content">
                    <div class="logo">⚠</div>
                        <h1>Cedro<span>Reporta</span></h1>
                        <p>Sua voz transforma a cidade</p>
                    <div class="stars">★★★★★</div>

                    <ul>
                        <li>Registre problemas urbanos facilmente</li>
                        <li>Acompanhe o status em tempo real</li>
                        <li>Contribua com Cedro-CE</li>
                    </ul>
            </div>

            <div class="skyline" aria-hidden="true">
                <span class="building b1"></span>
                <span class="building b2"></span>
                <span class="building b3"></span>
                <span class="building b4"></span>
                <span class="building b5"></span>
                <span class="building b6"></span>
                <span class="building b7"></span>
                <span class="building b8"></span>
                <span class="building b9"></span>
                <span class="building b10"></span>
                <span class="building b11"></span>
                <span class="building b12"></span>
            </div>
        </div>

        <div class="right">
            <div class="card">
                <div class="top">
                    <span>Município de Cedro — CE</span>
                    <img src="{{ asset('images/brasao-cedro.png') }}" alt="Brasão de Cedro" class="brand-mark">
                </div>

                <h2>Bem-vindo(a) de volta</h2>
                <p>Faça login para acessar sua conta</p>

                 <form>
                    <label for="email">E-MAIL</label>
                    <div class="input-group">
                        <input id="email" type="email" placeholder="usuario@gmail.com">
                        <span class="icon">✉</span>
                    </div>

                    <label for="senha">SENHA</label>
                    <div class="input-group">
                        <input id="senha" type="password" placeholder="******">
                        <span class="icon">🔒</span>
                    </div>

                    <a href="#" class="forgot">Esqueci minha senha</a>

                    <button class="primary" type="submit">Entrar</button>
                </form>

                <div class="divider">ou</div>

                <button class="secondary" type="button">Criar conta</button>
            </div>
        </div>
    </div>
</body>
</html>