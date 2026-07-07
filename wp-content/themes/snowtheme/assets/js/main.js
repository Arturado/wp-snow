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
    var searchBtn     = document.getElementById('filter-btn');
    var cards         = document.querySelectorAll('.evento-card');
    var noResults     = document.getElementById('no-results');
    var counter       = document.getElementById('eventos-counter');

    if (!countrySelect || !cards.length) return;

    function applyFilter() {
      var selectedPais = countrySelect.value;
      var selectedTipo = typeSelect.value;
      var visible = 0;

      cards.forEach(function (card) {
        var paisMatch = !selectedPais || card.dataset.pais === selectedPais;
        var tipoMatch = !selectedTipo || card.dataset.tipo === selectedTipo;

        if (paisMatch && tipoMatch) {
          card.style.display = '';
          visible++;
        } else {
          card.style.display = 'none';
        }
      });

      // Update "no results" message
      if (noResults) {
        noResults.style.display = visible === 0 ? 'block' : 'none';
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

// Mostrar más portfolio
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