(function () {
    'use strict';

    var header = document.querySelector('.site-header');
    var nav = document.querySelector('.nav');
    var toggle = document.querySelector('.nav-toggle');
    var backdrop = document.querySelector('.nav-backdrop');
    var yearEl = document.getElementById('year');

    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    function onScroll() {
        if (!header) return;
        header.classList.toggle('is-scrolled', window.scrollY > 24);
    }

    function closeNav() {
        if (nav) nav.classList.remove('is-open');
        if (backdrop) backdrop.classList.remove('is-visible');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    function openNav() {
        if (nav) nav.classList.add('is-open');
        if (backdrop) backdrop.classList.add('is-visible');
        if (toggle) toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (toggle) {
        toggle.addEventListener('click', function () {
            if (nav && nav.classList.contains('is-open')) {
                closeNav();
            } else {
                openNav();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeNav);
    }

    document.querySelectorAll('.nav a').forEach(function (link) {
        link.addEventListener('click', closeNav);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
    });
})();
