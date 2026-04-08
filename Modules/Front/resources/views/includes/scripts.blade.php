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

    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') {
                return;
            }

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                if (mobileMenuWrapper && mobileMenuWrapper.classList.contains('is-open')) {
                    setMobileMenuOpen(false);
                }
            }
        });
    });
</script>
