<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/password-recovery.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="recovery-shell">
        <aside class="recovery-hero" aria-label="Resumo do fluxo de recuperação de senha">
            <div class="hero-content">
                <div class="hero-brand">
                    <img src="{{ asset('logo-cedro.png') }}" alt="Brasão de Cedro">
                    <span>CedroReporta</span>
                </div>

                <div class="hero-kicker">
                    <i class="bi bi-shield-lock"></i>
                    Recuperação Direta
                </div>

                <h1 class="hero-title">Redefina seu acesso <span>agora</span></h1>

                <p class="hero-description">
                    Informe o seu e-mail cadastrado e escolha a sua nova senha para voltar a acessar o sistema imediatamente.
                </p>

                <ul class="hero-checklist">
                    <li><i class="bi bi-check-circle-fill"></i> Informe o e-mail da sua conta.</li>
                    <li><i class="bi bi-check-circle-fill"></i> Digite a nova senha desejada.</li>
                    <li><i class="bi bi-check-circle-fill"></i> Confirme e acesse o sistema.</li>
                </ul>
            </div>
        </aside>

        <section class="recovery-panel">
            <article class="recovery-card">
                <div class="recovery-card-header">
                    <div>
                        <div class="meta">Acesso</div>
                        <h2 class="card-title">Alterar minha senha</h2>
                    </div>
                </div>

                <p class="card-text">
                    Preencha os dados abaixo para redefinir a sua senha de acesso.
                </p>

                <!-- Mensagem de Sucesso -->
                @if (session('success'))
                    <div class="alert alert-success is-visible" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Mensagem de Erro Genérico (ex: E-mail não encontrado) -->
                @if (session('error'))
                    <div class="alert alert-error is-visible" role="alert" style="background-color: #fef2f2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px; display: flex; gap: 8px; border: 1px solid #f87171;">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form class="form-stack" action="{{ route('password.recovery.submit') }}" method="post">
                    @csrf
                    
                    <!-- Campo E-mail -->
                    <div class="form-field">
                        <label for="recovery-email">E-mail cadastrado</label>
                        <div class="input-shell">
                            <i class="bi bi-envelope"></i>
                            <input id="recovery-email" name="email" type="email" value="{{ old('email') }}" placeholder="usuario@exemplo.com" autocomplete="email" required>
                        </div>
                        @error('email')
                            <div class="inline-help" style="color: #dc2626; margin-top: 4px;">{{ $message }}</div>
                        @else
                            <div class="inline-help">O sistema verificará a qual conta este e-mail pertence.</div>
                        @enderror
                    </div>

                    <!-- Campo Nova Senha -->
                    <div class="form-field">
                        <label for="recovery-password">Nova Senha</label>
                        <div class="input-shell">
                            <i class="bi bi-lock"></i>
                            <input id="recovery-password" name="password" type="password" placeholder="Mínimo de 6 caracteres" required>
                        </div>
                        @error('password')
                            <div class="inline-help" style="color: #dc2626; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Campo Confirmar Nova Senha -->
                    <div class="form-field">
                        <label for="recovery-password-confirm">Confirmar Nova Senha</label>
                        <div class="input-shell">
                            <i class="bi bi-lock-fill"></i>
                            <input id="recovery-password-confirm" name="password_confirmation" type="password" placeholder="Repita a nova senha" required>
                        </div>
                    </div>

                    <button type="submit" class="primary-btn">Alterar Senha</button>
                </form>

                <a href="{{ route('login') }}" class="text-link">
                    <i class="bi bi-arrow-left"></i>
                    Voltar para o login
                </a>
            </article>
        </section>
    </main>
</body>
</html>