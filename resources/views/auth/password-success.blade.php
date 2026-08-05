<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Senha redefinida com sucesso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/password-recovery.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="recovery-shell">
        <aside class="recovery-hero" aria-label="Resumo da conclusão do fluxo">
            <div class="hero-content">
                <div class="hero-brand">
                    <img src="{{ asset('logo-cedro.png') }}" alt="Brasão de Cedro">
                    <span>CedroReporta</span>
                </div>

                <div class="hero-kicker">
                    <i class="bi bi-check-circle"></i>
                    Processo concluído
                </div>

                <h1 class="hero-title">Acesso <span>restabelecido</span></h1>

                <p class="hero-description">
                    O fluxo de recuperação foi finalizado com sucesso. Agora o usuário pode retornar à tela de login.
                </p>

                <ul class="hero-checklist">
                    <li><i class="bi bi-1-circle-fill"></i> A senha foi redefinida no cenário simulado.</li>
                    <li><i class="bi bi-2-circle-fill"></i> O estado temporário da recuperação é limpo ao voltar ao login.</li>
                    <li><i class="bi bi-3-circle-fill"></i> A jornada termina em uma confirmação visual clara.</li>
                </ul>
            </div>
        </aside>

        <section class="recovery-panel">
            <article class="recovery-card success-layout">
                <div class="recovery-card-header">
                    <div>
                        <div class="meta">Sucesso</div>
                        <h2 class="card-title">Senha redefinida com sucesso</h2>
                    </div>
                    <div class="badge">Etapa 3 de 3</div>
                </div>

                <div class="stepper" aria-label="Etapas da recuperação de senha">
                    <div class="step">
                        <strong>1</strong>
                        <span>Recuperar</span>
                    </div>
                    <div class="step">
                        <strong>2</strong>
                        <span>Redefinir</span>
                    </div>
                    <div class="step is-active">
                        <strong>3</strong>
                        <span>Sucesso</span>
                    </div>
                </div>

                <div class="success-visual" aria-hidden="true">
                    <i class="bi bi-check2-circle"></i>
                </div>

                @if (session('success'))
                    <div class="alert alert-success is-visible" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <p class="card-text">
                    Sua nova senha foi cadastrada com sucesso. Agora você já pode entrar novamente no sistema.
                </p>

                <div class="success-actions">
                    <a href="{{ route('login') }}" class="primary-btn">
                        Voltar para o Login
                    </a>
                </div>
            </article>
        </section>
    </main>
</body>
</html>