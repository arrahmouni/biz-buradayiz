(function () {
    function syncStagingBannerOffset() {
        if (!document.body || !document.body.classList.contains('app-env-staging')) {
            return;
        }
        var banner = document.querySelector('.app-staging-environment-banner');
        if (!banner) {
            return;
        }
        var b = banner.offsetHeight;
        document.documentElement.style.setProperty('--app-staging-banner-height', b + 'px');

        // Control panel (Metronic): position fixed #kt_toolbar below #kt_header when banner + header are stacked
        if (document.body.id === 'kt_body') {
            var header = document.getElementById('kt_header');
            if (header) {
                var stackTop = b + header.offsetHeight;
                document.documentElement.style.setProperty('--app-staging-kt-header-stack-top', stackTop + 'px');
            } else {
                document.documentElement.style.removeProperty('--app-staging-kt-header-stack-top');
            }
        }
    }

    function init() {
        if (!document.body || !document.body.classList.contains('app-env-staging')) {
            return;
        }
        syncStagingBannerOffset();
        if (window.ResizeObserver) {
            var el = document.querySelector('.app-staging-environment-banner');
            if (el) {
                var ro = new ResizeObserver(syncStagingBannerOffset);
                ro.observe(el);
            }
            if (document.body.id === 'kt_body') {
                var adminHeader = document.getElementById('kt_header');
                if (adminHeader) {
                    var ro2 = new ResizeObserver(syncStagingBannerOffset);
                    ro2.observe(adminHeader);
                }
            }
        } else {
            window.addEventListener('resize', syncStagingBannerOffset);
        }
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(syncStagingBannerOffset);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
