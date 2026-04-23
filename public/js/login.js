// Função para alternar a visibilidade da senha
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