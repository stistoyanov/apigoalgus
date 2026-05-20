(function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        var targetId = button.getAttribute('data-password-toggle');
        var input = document.getElementById(targetId);

        if (!input) {
            return;
        }

        button.addEventListener('click', function () {
            var showing = input.getAttribute('type') === 'text';
            input.setAttribute('type', showing ? 'password' : 'text');
            button.setAttribute('aria-pressed', showing ? 'false' : 'true');
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            button.classList.toggle('is-visible', !showing);
            input.focus({ preventScroll: true });
        });
    });
})();
