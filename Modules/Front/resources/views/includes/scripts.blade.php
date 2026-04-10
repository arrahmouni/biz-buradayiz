<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenuWrapper = document.getElementById('mobileMenuWrapper');

    function setMobileMenuOpen(open) {
        if (!menuBtn || !mobileMenuWrapper) {
            return;
        }
        mobileMenuWrapper.classList.toggle('is-open', open);
        menuBtn.classList.toggle('is-open', open);
        menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    if (menuBtn && mobileMenuWrapper) {
        menuBtn.addEventListener('click', () => {
            const next = !mobileMenuWrapper.classList.contains('is-open');
            setMobileMenuOpen(next);
        });
    }

    const langDropdown = document.getElementById('langDropdown');
    if (langDropdown) {
        document.addEventListener('click', (e) => {
            if (!langDropdown.open) {
                return;
            }
            if (!langDropdown.contains(e.target)) {
                langDropdown.removeAttribute('open');
            }
        });
    }

    document.querySelectorAll('a[href*="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const hrefAttr = this.getAttribute('href');
            if (!hrefAttr || hrefAttr === '#') {
                return;
            }

            let url;
            try {
                url = new URL(this.href);
            } catch {
                return;
            }

            if (url.origin !== window.location.origin) {
                return;
            }

            const hash = url.hash;
            if (!hash || hash === '#') {
                return;
            }

            if (url.pathname !== window.location.pathname || url.search !== window.location.search) {
                return;
            }

            const target = document.querySelector(hash);
            if (!target) {
                return;
            }

            e.preventDefault();
            const smoothScroll = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            target.scrollIntoView({ behavior: smoothScroll ? 'smooth' : 'auto' });
            history.pushState(null, '', hash);
            if (mobileMenuWrapper && mobileMenuWrapper.classList.contains('is-open')) {
                setMobileMenuOpen(false);
            }
        });
    });
</script>
