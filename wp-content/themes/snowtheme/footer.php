<footer class="snow-footer">
  <div class="container snow-footer__inner">
    <div class="snow-footer__brand">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="snow-logo snow-logo--lg" aria-label="SNOW* — Inicio">
        SNOW<span class="snow-logo__star">*</span>
      </a>
      <p class="snow-footer__tagline">Productora de eventos en 15 países.<br>Stand Up · Música · Festivales · Conferencias.</p>
    </div>

    <nav class="snow-footer__nav" aria-label="Menú del pie de página">
      <?php
      wp_nav_menu([
          'theme_location' => 'footer-menu',
          'container'      => false,
          'fallback_cb'    => false,
          'depth'          => 1,
      ]);
      ?>
    </nav>

  </div>
  <div class="snow-footer__bottom">
    <p>&copy; <?php echo esc_html(date('Y')); ?> SNOW<span class="snow-logo__star">*</span> · Todos los derechos reservados</p>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
