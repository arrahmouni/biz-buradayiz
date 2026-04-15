(function () {
    function hidePageLoader() {
        var loader = document.getElementById('pageLoader');
        if (!loader) {
            return;
        }
        loader.classList.add('page-loader--done');
        loader.setAttribute('aria-busy', 'false');
    }

    if (document.readyState === 'complete') {
        hidePageLoader();
    } else {
        window.addEventListener('load', hidePageLoader, { once: true });
    }

    window.hidePageLoader = hidePageLoader;

    var pageLoaderLogoAnchor = document.querySelector('[data-page-loader-logo-anchor]');
    if (pageLoaderLogoAnchor) {
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!reduceMotion) {
            pageLoaderLogoAnchor.addEventListener(
                'animationend',
                function (e) {
                    if (e.animationName !== 'page-loader-logo-enter') {
                        return;
                    }
                    pageLoaderLogoAnchor.classList.add('page-loader__logo-anchor--idle');
                },
                { once: true }
            );
        }
    }
})();
