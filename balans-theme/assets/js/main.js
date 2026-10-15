(function () {
    'use strict';

    // Mobile nav toggle
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('.nav-toggle');
        var nav = document.querySelector('.primary-nav');
        if (toggle && nav) {
            toggle.addEventListener('click', function () {
                var isOpen = nav.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        // Time slot picker
        document.querySelectorAll('[data-time-slots]').forEach(function (group) {
            var hidden = group.parentElement.querySelector('input[name="time_slot"]');
            group.querySelectorAll('.time-slot').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    group.querySelectorAll('.time-slot').forEach(function (b) { b.classList.remove('is-selected'); });
                    btn.classList.add('is-selected');
                    if (hidden) { hidden.value = btn.dataset.value; }
                });
            });
        });

        var T = (window.BalansData && window.BalansData.i18n) || {};

        // AJAX forms
        document.querySelectorAll('form[data-balans-ajax]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var action = form.dataset.balansAjax;
                var messageEl = form.querySelector('.balans-form-message');
                var submit = form.querySelector('button[type="submit"]');
                if (messageEl) { messageEl.textContent = ''; messageEl.className = 'balans-form-message'; }
                if (submit) { submit.disabled = true; submit.dataset.label = submit.textContent; submit.textContent = (T.wait || 'Even geduld…'); }

                var formData = new FormData(form);
                formData.append('action', action);
                formData.append('nonce', window.BalansData ? window.BalansData.nonce : '');
                if (window.BalansData && window.BalansData.lang) { formData.append('lang', window.BalansData.lang); }

                fetch((window.BalansData && window.BalansData.ajaxUrl) || '/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (submit) { submit.disabled = false; submit.textContent = submit.dataset.label || T.send || 'Verzenden'; }
                    if (!messageEl) { return; }
                    if (data && data.success) {
                        messageEl.className = 'balans-form-message balans-form-message--success';
                        messageEl.textContent = data.data && data.data.message ? data.data.message : (T.thanks || 'Bedankt, we hebben je bericht ontvangen.');
                        form.reset();
                        form.querySelectorAll('.time-slot.is-selected').forEach(function (b) { b.classList.remove('is-selected'); });
                    } else {
                        messageEl.className = 'balans-form-message balans-form-message--error';
                        var errors = (data && data.data && data.data.errors) ? data.data.errors : [T.error || 'Er ging iets mis.'];
                        messageEl.textContent = errors.join(' ');
                    }
                })
                .catch(function () {
                    if (submit) { submit.disabled = false; submit.textContent = submit.dataset.label || T.send || 'Verzenden'; }
                    if (messageEl) {
                        messageEl.className = 'balans-form-message balans-form-message--error';
                        messageEl.textContent = (T.network || 'Netwerkfout. Probeer het later opnieuw.');
                    }
                });
            });
        });
    });
})();
