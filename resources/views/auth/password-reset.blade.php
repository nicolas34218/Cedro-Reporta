<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/password-recovery.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="recovery-shell">
        <aside class="recovery-hero" aria-label="Resumo do passo de redefinição">
            <div class="hero-content">
                <div class="hero-brand">
                    <img src="{{ asset('logo-cedro.png') }}" alt="Brasão de Cedro">
                    <span>CedroReporta</span>
                </div>

                <div class="hero-kicker">
                    <i class="bi bi-key"></i>
                    Etapa de redefinição
                </div>

                <h1 class="hero-title">Defina uma <span>nova senha</span></h1>

                <p class="hero-description">
                    Nesta tela simulada, o usuário confirma a nova senha antes de concluir o fluxo e voltar ao login.
                </p>

                <ul class="hero-checklist">
                    <li><i class="bi bi-check2-circle"></i> O e-mail escolhido na etapa anterior é mostrado para reforçar o contexto.</li>
                    <li><i class="bi bi-check2-circle"></i> A senha precisa ser digitada e confirmada com a mesma informação.</li>
                    <li><i class="bi bi-check2-circle"></i> Após a validação, o sistema navega para a tela de sucesso.</li>
                </ul>
            </div>
        </aside>

        <section class="recovery-panel">
            <article class="recovery-card">
                <div class="recovery-card-header">
                    <div>
                        <div class="meta">Acesso</div>
                        <h2 class="card-title">Redefinição de senha</h2>
                    </div>
                    <div class="badge">Etapa 2 de 3</div>
                </div>

                <div class="stepper" aria-label="Etapas da recuperação de senha">
                    <div class="step">
                        <strong>1</strong>
                        <span>Recuperar</span>
                    </div>
                    <div class="step is-active">
                        <strong>2</strong>
                        <span>Redefinir</span>
                    </div>
                    <div class="step">
                        <strong>3</strong>
                        <span>Sucesso</span>
                    </div>
                </div>

                <p class="card-text">
                    Conta localizada para <strong>{{ $recoveryEmail }}</strong>. Defina a nova senha para continuar.
                </p>

                @if (session('success'))
                    <div class="alert alert-success is-visible" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @error('password')
                    <div class="alert alert-error is-visible" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <form class="form-stack" action="{{ url('/recuperar-senha/redefinir') }}" method="post">
                    @csrf
                    <div class="form-field">
                        <label for="new-password">Nova senha</label>
                        <div class="input-shell">
                            <i class="bi bi-lock"></i>
                            <input id="new-password" name="password" type="password" placeholder="Digite a nova senha" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="confirm-password">Confirmar nova senha</label>
                        <div class="input-shell">
                            <i class="bi bi-lock-fill"></i>
                            <input id="confirm-password" name="password_confirmation" type="password" placeholder="Repita a nova senha" autocomplete="new-password" required>
                        </div>
                    </div>

                    <button type="submit" class="primary-btn">Redefinir Senha</button>
                </form>

                <a href="{{ url('/esqueci-senha') }}" class="text-link">
                    <i class="bi bi-arrow-left"></i>
                    Voltar e informar outro e-mail
                </a>
            </article>
        </section>
    </main>
</body>
</html>