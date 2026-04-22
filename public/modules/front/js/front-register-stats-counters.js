(function () {
    'use strict';

    var root = document.querySelector('.js-register-stats-bar');
    if (!root) {
        return;
    }

    var counters = root.querySelectorAll('.js-prl-stat-counter');
    if (!counters.length) {
        return;
    }

    var locale = root.getAttribute('data-stats-locale') || document.documentElement.lang || 'en';
    var prefersReduced =
        typeof window.matchMedia === 'function' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    function formatFloatForLocale(n) {
        return new Intl.NumberFormat(locale, { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(n);
    }

    function applyFinals() {
        counters.forEach(function (el) {
            var finalText = el.getAttribute('data-counter-final');
            if (finalText) {
                el.textContent = finalText;
            }
        });
    }

    if (prefersReduced) {
        applyFinals();
        return;
    }

    function runCounter(el, durationMs, done) {
        var type = el.getAttribute('data-counter-type') || 'int';
        var target = parseFloat(String(el.getAttribute('data-counter-target') || '0').replace(/,/g, '.'));
        var suffix = el.getAttribute('data-counter-suffix') || '';
        if (Number.isNaN(target)) {
            applyFinals();
            done();
            return;
        }

        var start = null;

        function frame(ts) {
            if (start === null) {
                start = ts;
            }
            var elapsed = ts - start;
            var p = Math.min(1, elapsed / durationMs);
            var eased = easeOutCubic(p);
            var current = target * eased;

            if (type === 'float') {
                el.textContent = formatFloatForLocale(current) + suffix;
            } else {
                el.textContent = Math.round(current) + suffix;
            }

            if (p < 1) {
                requestAnimationFrame(frame);
            } else {
                if (type === 'float') {
                    el.textContent = formatFloatForLocale(target) + suffix;
                } else {
                    el.textContent = Math.round(target) + suffix;
                }
                done();
            }
        }

        requestAnimationFrame(frame);
    }

    var started = false;

    function startAll() {
        if (started) {
            return;
        }
        started = true;
        var baseDuration = 1100;
        var step = 90;
        counters.forEach(function (el, i) {
            setTimeout(function () {
                runCounter(el, baseDuration, function () {});
            }, i * step);
        });
    }

    if (!window.IntersectionObserver) {
        startAll();
        return;
    }

    var observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    startAll();
                    observer.disconnect();
                }
            });
        },
        { root: null, rootMargin: '0px 0px -5% 0px', threshold: 0.2 }
    );

    observer.observe(root);
})();
