<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                        <i class="bi bi-envelope input-icon" aria-hidden="true"></i>
                        <input id="email" name="email" type="email" placeholder="usuario@gmail.com" autocomplete="email">
                    </div>  

                    <label for="senha">SENHA</label>
                    <div class="input-group">
                        <i class="bi bi-lock input-icon" aria-hidden="true"></i>
                        <input id="senha" name="senha" type="password" placeholder="******" autocomplete="current-password">
                        <button type="button" class="toggle-password" aria-label="Mostrar senha" aria-pressed="false">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <a href="#" class="forgot">Esqueci minha senha</a>

                    <button class="primary" type="submit">Entrar</button>
                </form>

                <div class="divider">ou</div>

                <button class="secondary" type="button">Criar conta</button>
            </div>
        </div>
    </div>

<script>
    const toggleBtn = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#senha');

    if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            this.setAttribute('aria-pressed', String(isPassword));
            this.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');

            if (icon) {
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            }
        });
    }
</script>

</body>
</html>