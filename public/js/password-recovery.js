document.addEventListener('DOMContentLoaded', () => {
    const flow = document.querySelector('[data-password-flow]');

    if (!flow) {
        return;
    }

    const recoveryEmailKey = 'cedroReporta.recovery.email';
    const recoveryReadyKey = 'cedroReporta.recovery.ready';
    const recoveryDoneKey = 'cedroReporta.recovery.done';

    const loginUrl = flow.dataset.loginUrl;
    const recoveryUrl = flow.dataset.recoveryUrl;

    const clearRecoveryState = () => {
        window.sessionStorage.removeItem(recoveryEmailKey);
        window.sessionStorage.removeItem(recoveryReadyKey);
        window.sessionStorage.removeItem(recoveryDoneKey);
    };

    const showAlert = (element, message) => {
        if (!element) {
            return;
        }

        element.innerHTML = message;
        element.classList.add('is-visible');
    };

    const hideAlert = (element) => {
        if (!element) {
            return;
        }

        element.innerHTML = '';
        element.classList.remove('is-visible');
    };

    const normalizeEmail = (value) => value.trim().toLowerCase();

    const goTo = (url, email = null) => {
        if (!url) {
            return;
        }

        if (!email) {
            window.location.href = url;
            return;
        }

        const target = new URL(url, window.location.origin);
        target.searchParams.set('email', email);
        window.location.href = target.toString();
    };

    const recoveryForm = flow.querySelector('[data-recovery-form]');
    if (recoveryForm) {
        const allowedEmails = (recoveryForm.dataset.mockEmails || '')
            .split(',')
            .map((email) => normalizeEmail(email))
            .filter(Boolean);

        const recoveryError = flow.querySelector('[data-recovery-error]');
        const recoveryEmailInput = recoveryForm.querySelector('input[name="email"]');

        recoveryForm.addEventListener('submit', (event) => {
            event.preventDefault();
            hideAlert(recoveryError);

            const email = normalizeEmail(recoveryEmailInput?.value || '');

            if (!email) {
                showAlert(recoveryError, '<i class="bi bi-exclamation-circle"></i> Informe o e-mail cadastrado para continuar.');
                return;
            }

            if (!allowedEmails.includes(email)) {
                showAlert(recoveryError, '<i class="bi bi-exclamation-circle"></i> Nenhuma conta foi encontrada com esse e-mail.');
                return;
            }

            window.sessionStorage.setItem(recoveryEmailKey, email);
            window.sessionStorage.setItem(recoveryReadyKey, 'true');
            window.sessionStorage.removeItem(recoveryDoneKey);

            goTo(recoveryForm.action, email);
        });
    }

    const resetForm = flow.querySelector('[data-reset-form]');
    if (resetForm) {
        const email = window.sessionStorage.getItem(recoveryEmailKey);
        const ready = window.sessionStorage.getItem(recoveryReadyKey) === 'true';

        if (!email || !ready) {
            goTo(recoveryUrl || loginUrl);
            return;
        }

        const emailTarget = flow.querySelector('[data-recovery-email]');
        if (emailTarget) {
            emailTarget.textContent = email;
        }

        const resetError = flow.querySelector('[data-reset-error]');
        const passwordInput = resetForm.querySelector('input[name="password"]');
        const confirmationInput = resetForm.querySelector('input[name="password_confirmation"]');

        resetForm.addEventListener('submit', (event) => {
            event.preventDefault();
            hideAlert(resetError);

            const password = passwordInput?.value || '';
            const confirmation = confirmationInput?.value || '';

            if (password.length < 6) {
                showAlert(resetError, '<i class="bi bi-exclamation-circle"></i> A nova senha deve ter pelo menos 6 caracteres.');
                return;
            }

            if (password !== confirmation) {
                showAlert(resetError, '<i class="bi bi-exclamation-circle"></i> A confirmação da senha não corresponde.');
                return;
            }

            window.sessionStorage.setItem(recoveryDoneKey, 'true');
            goTo(resetForm.action);
        });
    }

    const successLink = flow.querySelector('[data-clear-recovery]');
    if (successLink) {
        const completed = window.sessionStorage.getItem(recoveryDoneKey) === 'true';

        if (!completed) {
            goTo(recoveryUrl || loginUrl);
            return;
        }

        successLink.addEventListener('click', () => {
            clearRecoveryState();
        });
    }
});