<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/page-loader.js') }}?v={{$_STYLE_VER_}}"></script>
@if (isStaging())
    <script src="{{ asset('js/staging-environment-banner.js') }}?v={{$_STYLE_VER_}}"></script>
@endif

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
    const servicesNavDropdown = document.getElementById('servicesNavDropdown');
    const providerAccountNav = document.getElementById('providerAccountNav');

    function closeDetailsIfOpen(detailsEl) {
        if (detailsEl && detailsEl.open) {
            detailsEl.removeAttribute('open');
        }
    }

    function closeOtherNavDetails(except) {
        if (except !== langDropdown) {
            closeDetailsIfOpen(langDropdown);
        }
        if (except !== servicesNavDropdown) {
            closeDetailsIfOpen(servicesNavDropdown);
        }
        if (except !== providerAccountNav) {
            closeDetailsIfOpen(providerAccountNav);
        }
    }

    if (langDropdown) {
        langDropdown.addEventListener('toggle', () => {
            if (langDropdown.open) {
                closeOtherNavDetails(langDropdown);
            }
        });
    }
    if (servicesNavDropdown) {
        servicesNavDropdown.addEventListener('toggle', () => {
            if (servicesNavDropdown.open) {
                closeOtherNavDetails(servicesNavDropdown);
            }
        });
    }
    if (providerAccountNav) {
        providerAccountNav.addEventListener('toggle', () => {
            if (providerAccountNav.open) {
                closeOtherNavDetails(providerAccountNav);
            }
        });
    }

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

    if (servicesNavDropdown) {
        document.addEventListener('click', (e) => {
            if (!servicesNavDropdown.open) {
                return;
            }
            if (!servicesNavDropdown.contains(e.target)) {
                servicesNavDropdown.removeAttribute('open');
            }
        });
    }

    if (providerAccountNav) {
        document.addEventListener('click', (e) => {
            if (!providerAccountNav.open) {
                return;
            }
            if (!providerAccountNav.contains(e.target)) {
                providerAccountNav.removeAttribute('open');
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') {
            return;
        }
        if (menuBtn && mobileMenuWrapper && mobileMenuWrapper.classList.contains('is-open')) {
            setMobileMenuOpen(false);
            return;
        }
        closeDetailsIfOpen(langDropdown);
        closeDetailsIfOpen(servicesNavDropdown);
        closeDetailsIfOpen(providerAccountNav);
    });

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

<script src="{{ asset('modules/front/js/front-form-submit-loading.js') }}?v={{$_STYLE_VER_}}"></script>
<script src="{{ asset('modules/front/js/front-provider-location-select.js') }}?v={{$_STYLE_VER_}}"></script>
<script src="{{ asset('modules/front/js/front-scroll-reveal.js') }}?v={{$_STYLE_VER_}}"></script>
