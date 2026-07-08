/* Snow Theme — main.js */
(function () {
  'use strict';

  /* ---- Mobile nav toggle ---- */
  function initNav() {
    var toggle = document.getElementById('nav-toggle');
    var nav    = document.getElementById('snow-nav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on nav link click
    nav.querySelectorAll('.snow-nav__link').forEach(function (link) {
      link.addEventListener('click', function () {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      });
    });
  }

  /* ---- Sticky header shadow ---- */
  function initHeaderScroll() {
    var header = document.getElementById('snow-header');
    if (!header) return;
    window.addEventListener('scroll', function () {
      header.style.boxShadow = window.scrollY > 10
        ? '0 2px 24px rgba(0,0,0,0.6)'
        : '';
    }, { passive: true });
  }

  /* ---- Events filter ---- */
  function initFilter() {
    var countrySelect = document.getElementById('filter-pais');
    var typeSelect    = document.getElementById('filter-tipo');
    var searchInput   = document.getElementById('filter-search');
    var searchBtn     = document.getElementById('filter-btn');
    var cards         = document.querySelectorAll('#eventos-grid .evento-card');
    var noResults     = document.getElementById('no-results');
    var counter       = document.getElementById('eventos-counter');
    var searchTimeout;

    if (!countrySelect || !cards.length) return;

    function applyFilter() {
      var selectedPais = countrySelect.value;
      var selectedTipo = typeSelect.value;
      var texto        = (searchInput ? searchInput.value : '').toLowerCase().trim();
      var hayFiltro    = !!(selectedPais || selectedTipo || texto);
      var visible = 0;

      // Progreso actual de la paginación (puede haber avanzado por clicks en "Ver más")
      var loadMoreBtn      = document.getElementById('eventos-loadmore');
      var paginatedVisible = loadMoreBtn ? parseInt(loadMoreBtn.dataset.visible) : cards.length;

      cards.forEach(function (card, idx) {
        var paisMatch   = !selectedPais || card.dataset.pais === selectedPais;
        var tipoMatch   = !selectedTipo || card.dataset.tipo === selectedTipo;
        var cardTitulo  = (card.dataset.titulo  || '').toLowerCase();
        var cardTalento = (card.dataset.talento || '').toLowerCase();
        var cardCiudad  = (card.dataset.ciudad || '').toLowerCase();
        var textoMatch  = !texto || cardTitulo.indexOf(texto) !== -1 || cardTalento.indexOf(texto) !== -1 || cardCiudad.indexOf(texto) !== -1;
        var matchFiltro = paisMatch && tipoMatch && textoMatch;

        if (hayFiltro) {
          // Con filtro activo: ignorar la paginación, mostrar todas las que matchean
          card.classList.remove('evento-card--hidden');
          card.style.display = matchFiltro ? '' : 'none';
        } else {
          // Sin filtro: restaurar el estado de paginación según el progreso actual
          card.style.display = '';
          if (idx < paginatedVisible) {
            card.classList.remove('evento-card--hidden');
          } else {
            card.classList.add('evento-card--hidden');
          }
        }

        if (matchFiltro || !hayFiltro) visible++;
      });

      // Ocultar el botón "Ver más" mientras haya un filtro activo
      var loadMoreWrap = document.getElementById('eventos-loadmore-wrap');
      if (loadMoreWrap) {
        loadMoreWrap.style.display = hayFiltro ? 'none' : '';
      }

      // Update "no results" message
      if (noResults) {
        noResults.style.display = (hayFiltro && visible === 0) ? 'block' : 'none';
      }

      // Recalculate counter
      if (counter) {
        var countries = new Set();
        cards.forEach(function (card) {
          if (card.style.display !== 'none') {
            countries.add(card.dataset.pais);
          }
        });
        var c = countries.size;
        counter.textContent =
          visible + ' evento' + (visible !== 1 ? 's' : '') +
          ' · ' +
          c + ' país' + (c !== 1 ? 'es' : '');
      }
    }

    if (searchBtn)     searchBtn.addEventListener('click', applyFilter);
    if (countrySelect) countrySelect.addEventListener('change', applyFilter);
    if (typeSelect)    typeSelect.addEventListener('change', applyFilter);
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilter, 200);
      });
    }
  }

  /* ---- Boot ---- */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  function boot() {
    initNav();
    initHeaderScroll();
    initFilter();
  }
})();

// Mostrar más portfolio (historial en single-talento.php)
const loadMoreBtn = document.getElementById('portfolio-loadmore');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', () => {
        document.querySelectorAll('.portfolio-card--hidden').forEach((card, i) => {
            card.classList.remove('portfolio-card--hidden');
            card.style.animation = `fadeInUp 0.4s ease ${i * 0.08}s both`;
        });
        document.getElementById('portfolio-loadmore-wrap').remove();
    });
}

// ===================== VER MÁS GENÉRICO (eventos / talentos) =====================
function initLoadMore(btnId, wrapId, counterId, hiddenClass) {
    const btn = document.getElementById(btnId);
    if (!btn) return;

    btn.addEventListener('click', () => {
        const step  = parseInt(btn.dataset.step) || 4;
        const total = parseInt(btn.dataset.total) || 0;
        let visible = parseInt(btn.dataset.visible) || 8;

        const grid = document.getElementById(btn.dataset.grid || 'eventos-grid');
        const hiddenCards = grid ? grid.querySelectorAll('.' + hiddenClass) : [];
        let shown = 0;

        hiddenCards.forEach(card => {
            if (shown < step) {
                card.classList.remove(hiddenClass);
                card.style.animation = `fadeInUp 0.4s ease ${shown * 0.08}s both`;
                shown++;
                visible++;
            }
        });

        btn.dataset.visible = visible;

        const remaining = total - visible;
        const counter = document.getElementById(counterId);
        if (counter) counter.textContent = remaining + (btnId.includes('talento') ? ' talentos más' : ' shows más');

        if (visible >= total || (grid && grid.querySelectorAll('.' + hiddenClass).length === 0)) {
            document.getElementById(wrapId)?.remove();
        }
    });
}

initLoadMore('eventos-loadmore',  'eventos-loadmore-wrap',  'eventos-loadmore-counter',  'evento-card--hidden');
initLoadMore('talentos-loadmore', 'talentos-loadmore-wrap', 'talentos-loadmore-counter', 'evento-card--hidden');

// ===================== MOBILE MENU (header centrado) =====================
const hamburger = document.getElementById('snow-hamburger');
const mobileNav = document.getElementById('snow-nav');
const overlay   = document.getElementById('snow-nav-overlay');

function openMobileMenu() {
    mobileNav?.classList.add('is-open');
    hamburger?.classList.add('is-active');
    overlay?.classList.add('is-active');
    hamburger?.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    mobileNav?.classList.remove('is-open');
    hamburger?.classList.remove('is-active');
    overlay?.classList.remove('is-active');
    hamburger?.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
}

hamburger?.addEventListener('click', () => {
    hamburger.classList.contains('is-active') ? closeMobileMenu() : openMobileMenu();
});

overlay?.addEventListener('click', closeMobileMenu);

// Cerrar al hacer click en cualquier link del nav
mobileNav?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', closeMobileMenu);
});

// Cerrar con Escape
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeMobileMenu();
});