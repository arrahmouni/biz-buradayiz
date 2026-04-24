(function () {
    var LABEL_ID = 'front-submit-processing-label';

    function getProcessingText() {
        var el = document.getElementById(LABEL_ID);
        return el && el.textContent ? el.textContent.trim() : '…';
    }

    function applyLoadingToButton(btn, processingText) {
        if (btn.classList.contains('front-submit-is-loading')) {
            return;
        }

        btn.classList.add('front-submit-is-loading');
        btn.setAttribute('aria-busy', 'true');
        btn.disabled = true;
        btn.classList.add('pointer-events-none', 'opacity-90');

        if (btn.tagName === 'INPUT' && String(btn.type).toLowerCase() === 'submit') {
            if (!btn.dataset.frontSubmitOriginalValue) {
                btn.dataset.frontSubmitOriginalValue = btn.value;
            }
            btn.value = processingText;
            return;
        }

        var spin = document.createElement('i');
        spin.className = 'fas fa-spinner fa-spin mr-2';
        spin.setAttribute('aria-hidden', 'true');

        var label = document.createElement('span');
        label.className = 'front-submit-loading-label';
        label.textContent = processingText;

        btn.replaceChildren(spin, label);
    }

    function onDocumentSubmit(e) {
        var form = e.target;
        if (!form || form.nodeName !== 'FORM') {
            return;
        }
        if (form.hasAttribute('data-skip-submit-loading')) {
            return;
        }

        var processingText = getProcessingText();

        window.setTimeout(function () {
            if (e.defaultPrevented) {
                return;
            }

            var selector = 'button[type="submit"], input[type="submit"]';
            var buttons = form.querySelectorAll(selector);
            if (!buttons.length) {
                return;
            }

            for (var i = 0; i < buttons.length; i += 1) {
                applyLoadingToButton(buttons[i], processingText);
            }
        }, 0);
    }

    function init() {
        document.addEventListener('submit', onDocumentSubmit, false);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
