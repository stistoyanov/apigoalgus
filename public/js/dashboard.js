(function () {
    var toggle = document.querySelector('.dashboard-menu-toggle');
    var shell = document.getElementById('dashboard-shell');
    var sidebar = document.getElementById('dashboard-sidebar');

    if (!toggle || !shell || !sidebar) {
        return;
    }

    toggle.addEventListener('click', function () {
        var open = shell.classList.toggle('sidebar-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    });

    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.matchMedia('(max-width: 768px)').matches) {
                shell.classList.remove('sidebar-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Open menu');
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && shell.classList.contains('sidebar-open')) {
            shell.classList.remove('sidebar-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
        }
    });
})();
