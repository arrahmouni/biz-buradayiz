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

    function initAuthFormSubmitState() {
        var el = document.getElementById('front-auth-submit-processing');
        var processingText = el && el.textContent ? el.textContent.trim() : '…';

        document.querySelectorAll('.front-auth-card-panel form').forEach(function (form) {
            if (String(form.method || '').toLowerCase() !== 'post') {
                return;
            }

            form.addEventListener('submit', function () {
                var btn = form.querySelector('button[type="submit"]');
                if (!btn || btn.disabled) {
                    return;
                }

                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');
                btn.classList.add('pointer-events-none', 'opacity-90', 'cursor-wait');

                var spin = document.createElement('i');
                spin.className = 'fas fa-spinner fa-spin mr-2';
                spin.setAttribute('aria-hidden', 'true');

                var label = document.createElement('span');
                label.textContent = processingText;

                btn.replaceChildren(spin, label);
            });
        });
    }

    function boot() {
        initPasswordToggles();
        initAuthFormSubmitState();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
