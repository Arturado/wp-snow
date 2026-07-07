(function () {
    'use strict';

    // ===================== GLightbox =====================
    if (typeof GLightbox !== 'undefined') {
        GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
            slideEffect: 'fade',
        });
    }

    // ===================== Countdown =====================
    var cdEl = document.getElementById('snow-countdown');
    if (cdEl && typeof snowPDP !== 'undefined' && snowPDP.eventoDatetime) {
        var target = new Date(snowPDP.eventoDatetime).getTime();

        function updateCountdown() {
            var now  = Date.now();
            var diff = target - now;

            if (diff <= 0) {
                cdEl.innerHTML = '<div class="snow-countdown__ended">¡El show ya comenzó!</div>';
                return;
            }

            var days  = Math.floor(diff / 86400000);
            var hours = Math.floor((diff % 86400000) / 3600000);
            var mins  = Math.floor((diff % 3600000) / 60000);
            var secs  = Math.floor((diff % 60000) / 1000);

            document.getElementById('cd-days').textContent  = String(days).padStart(2, '0');
            document.getElementById('cd-hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('cd-mins').textContent  = String(mins).padStart(2, '0');
            document.getElementById('cd-secs').textContent  = String(secs).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // ===================== Modal suscripción =====================
    var checkbox   = document.querySelector('.snow-suscribir-checkbox');
    var overlay    = document.getElementById('snow-modal-overlay');
    var closeBtn   = document.getElementById('snow-modal-close');
    var submitBtn  = document.getElementById('snow-modal-submit');
    var msgEl      = document.getElementById('snow-modal-msg');
    var nombreSpan = document.getElementById('modal-talento-nombre');

    function openModal() {
        if (!overlay) return;
        var talento = (checkbox && checkbox.dataset.talentoNombre) || '';
        if (nombreSpan) nombreSpan.textContent = talento;
        overlay.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(function () {
            var firstInput = document.getElementById('snow-nombre');
            if (firstInput) firstInput.focus();
        }, 100);
    }

    function closeModal() {
        if (!overlay) return;
        overlay.setAttribute('hidden', '');
        document.body.style.overflow = '';
        if (checkbox) checkbox.checked = false;
    }

    function showMsg(text, type) {
        if (!msgEl) return;
        msgEl.textContent = text;
        msgEl.className   = 'snow-modal__msg snow-modal__msg--' + type;
        msgEl.removeAttribute('hidden');
    }

    if (checkbox) {
        checkbox.addEventListener('change', function (e) {
            if (e.target.checked) openModal();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay && !overlay.hasAttribute('hidden')) closeModal();
    });

    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var nombre   = (document.getElementById('snow-nombre')?.value   || '').trim();
            var apellido = (document.getElementById('snow-apellido')?.value || '').trim();
            var email    = (document.getElementById('snow-email')?.value    || '').trim();

            if (!nombre || !apellido || !email) {
                showMsg('Por favor completá todos los campos.', 'error');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMsg('Ingresá un email válido.', 'error');
                return;
            }

            submitBtn.disabled     = true;
            submitBtn.textContent  = 'Enviando...';

            var data = new FormData();
            data.append('action',         'snow_suscribir');
            data.append('nonce',          snowPDP.nonce);
            data.append('nombre',         nombre);
            data.append('apellido',       apellido);
            data.append('email',          email);
            data.append('talento_id',     (checkbox && checkbox.dataset.talentoId)     || '');
            data.append('talento_nombre', (checkbox && checkbox.dataset.talentoNombre) || '');

            fetch(snowPDP.ajaxUrl, { method: 'POST', body: data })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        showMsg('¡Listo! Te avisamos cuando haya novedades. 🎉', 'success');
                        submitBtn.textContent = '¡Suscripto!';
                        setTimeout(closeModal, 2500);
                    } else {
                        showMsg((res.data && res.data.message) || 'Ocurrió un error. Intentá de nuevo.', 'error');
                        submitBtn.disabled    = false;
                        submitBtn.textContent = 'Suscribirme';
                    }
                })
                .catch(function () {
                    showMsg('Error de conexión. Intentá de nuevo.', 'error');
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Suscribirme';
                });
        });
    }

})();
