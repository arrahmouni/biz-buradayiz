(function () {
    'use strict';

    if (!window.IntersectionObserver) {
        document.querySelectorAll('.js-front-reveal').forEach(function (el) {
            el.classList.add('is-visible');
        });
        document.querySelectorAll('.js-front-reveal-group').forEach(function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function revealAll() {
        document.querySelectorAll('.js-front-reveal').forEach(function (el) {
            el.classList.add('is-visible');
        });
        document.querySelectorAll('.js-front-reveal-group').forEach(function (el) {
            el.classList.add('is-visible');
        });
    }

    if (prefersReduced) {
        revealAll();
        return;
    }

    function isRoughlyInViewport(el) {
        var rect = el.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight;
        return rect.top < vh * 0.92 && rect.bottom > 0;
    }

    var observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { root: null, rootMargin: '0px 0px -6% 0px', threshold: 0.06 }
    );

    document.querySelectorAll('.js-front-reveal').forEach(function (el) {
        if (isRoughlyInViewport(el)) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });
    document.querySelectorAll('.js-front-reveal-group').forEach(function (el) {
        if (isRoughlyInViewport(el)) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });
})();
