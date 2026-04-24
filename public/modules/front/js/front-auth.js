(function () {
    function initPasswordToggles() {
        document.querySelectorAll('[data-auth-password-toggle]').forEach(function (btn) {
            var inputId = btn.getAttribute('aria-controls');
            if (!inputId) {
                return;
            }
            var input = document.getElementById(inputId);
            if (!input) {
                return;
            }
            var icon = btn.querySelector('i');
            btn.addEventListener('click', function () {
                var isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                if (icon) {
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            });
        });
    }

    function boot() {
        initPasswordToggles();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
