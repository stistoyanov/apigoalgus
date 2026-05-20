(function () {
    var toggle = document.querySelector('.dashboard-menu-toggle');
    var shell = document.getElementById('dashboard-shell');
    var sidebar = document.getElementById('dashboard-sidebar');

    if (toggle && shell && sidebar) {
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
    }
})();

(function () {
    var openModals = [];

    function getModal(id) {
        return document.getElementById(id + '-modal');
    }

    function openModal(id, trigger) {
        var modal = getModal(id);
        if (!modal) return;

        if (id === 'user-edit') {
            populateEditModal(modal, trigger);
        } else if (id === 'user-delete') {
            populateDeleteModal(modal, trigger);
        }

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        openModals.push(modal);

        var focusable = modal.querySelector('input:not([type="hidden"]):not([disabled]), select, textarea, button');
        if (focusable) setTimeout(function () { focusable.focus(); }, 30);
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        openModals = openModals.filter(function (m) { return m !== modal; });
        if (openModals.length === 0) {
            document.body.classList.remove('modal-open');
        }
    }

    function populateEditModal(modal, trigger) {
        if (!trigger) return;
        var form = modal.querySelector('form');
        if (!form) return;

        form.setAttribute('action', trigger.dataset.action || '');

        var fields = {
            'edit-name': trigger.dataset.userName || '',
            'edit-email': trigger.dataset.userEmail || '',
            'edit-role': trigger.dataset.userRole || '',
            'edit-password': '',
            'edit-password-confirm': '',
        };
        Object.keys(fields).forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.value = fields[id];
        });

        var active = document.getElementById('edit-active');
        if (active) active.checked = trigger.dataset.userActive === '1';

        var roleSelect = document.getElementById('edit-role');
        var roleHint = document.getElementById('edit-role-hint');
        var isMaster = trigger.dataset.userMaster === '1';
        if (roleSelect) roleSelect.disabled = false;
        if (roleHint) roleHint.hidden = true;
        if (isMaster && roleSelect) {
            roleSelect.disabled = true;
            if (roleHint) roleHint.hidden = false;
        }
    }

    function populateDeleteModal(modal, trigger) {
        if (!trigger) return;
        var form = modal.querySelector('form');
        if (form) form.setAttribute('action', trigger.dataset.action || '');
        var emailEl = document.getElementById('delete-user-email');
        if (emailEl) emailEl.textContent = trigger.dataset.userEmail || '';
    }

    document.addEventListener('click', function (event) {
        var opener = event.target.closest('[data-modal-open]');
        if (opener) {
            event.preventDefault();
            openModal(opener.dataset.modalOpen, opener);
            return;
        }

        var closer = event.target.closest('[data-modal-close]');
        if (closer) {
            event.preventDefault();
            closeModal(closer.closest('.modal'));
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && openModals.length > 0) {
            closeModal(openModals[openModals.length - 1]);
        }
    });

    document.querySelectorAll('.modal[data-open-on-load="1"]').forEach(function (modal) {
        var id = modal.id.replace(/-modal$/, '');
        openModal(id, null);
    });
})();
