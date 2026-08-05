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
                    Recuperação guiada
                </div>

                <h1 class="hero-title">Recupere seu acesso em <span>3 passos</span></h1>

                <p class="hero-description">
                    Use este fluxo simulado para validar e-mail, redefinir a senha e voltar à tela de login com uma confirmação visual clara.
                </p>

                <ul class="hero-checklist">
                    <li><i class="bi bi-1-circle-fill"></i> Confirme o e-mail cadastrado para verificar se existe uma conta associada.</li>
                    <li><i class="bi bi-2-circle-fill"></i> Redefina a senha em uma tela separada, com confirmação de digitação.</li>
                    <li><i class="bi bi-3-circle-fill"></i> Receba uma mensagem de sucesso e volte ao login quando terminar.</li>
                </ul>
            </div>
        </aside>

        <section class="recovery-panel">
            <article class="recovery-card">
                <div class="recovery-card-header">
                    <div>
                        <div class="meta">Acesso</div>
                        <h2 class="card-title">Esqueci minha senha</h2>
                    </div>
                    <div class="badge">Etapa 1 de 3</div>
                </div>

                <div class="stepper" aria-label="Etapas da recuperação de senha">
                    <div class="step is-active">
                        <strong>1</strong>
                        <span>Recuperar</span>
                    </div>
                    <div class="step">
                        <strong>2</strong>
                        <span>Redefinir</span>
                    </div>
                    <div class="step">
                        <strong>3</strong>
                        <span>Sucesso</span>
                    </div>
                </div>

                <p class="card-text">
                    Informe o e-mail cadastrado para iniciarmos a recuperação de acesso.
                </p>

                @if (session('success'))
                    <div class="alert alert-success is-visible" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @error('email')
                    <div class="alert alert-error is-visible" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <form class="form-stack" action="{{ url('/esqueci-senha') }}" method="post">
                    @csrf
                    <div class="form-field">
                        <label for="recovery-email">E-mail cadastrado</label>
                        <div class="input-shell">
                            <i class="bi bi-envelope"></i>
                            <input id="recovery-email" name="email" type="email" value="{{ old('email') }}" placeholder="usuario@exemplo.com" autocomplete="email" required>
                        </div>
                        <div class="inline-help">O sistema verificará se o e-mail pertence a uma conta cadastrada.</div>
                    </div>

                    <button type="submit" class="primary-btn">Recuperar Senha</button>
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