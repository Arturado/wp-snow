<?php
if (!have_posts()) {
    wp_redirect(home_url('/404'));
    exit;
}
the_post();

$id           = get_the_ID();
$titulo       = get_the_title();
$subtitulo    = get_post_meta($id, '_evento_subtitulo', true);
$fecha_raw    = get_post_meta($id, '_evento_fecha', true);
$fecha_full   = $fecha_raw ? strtoupper(date_i18n('d M Y', strtotime($fecha_raw))) : '';
$hora         = get_post_meta($id, '_evento_hora', true);
$lugar        = get_post_meta($id, '_evento_lugar', true);
$ciudad       = get_post_meta($id, '_evento_ciudad', true);
$pais_bandera = get_post_meta($id, '_evento_pais_bandera', true);
$talento_data = snow_get_talento_data($id);
$descripcion  = get_post_meta($id, '_evento_descripcion', true) ?: get_the_excerpt();
$link_compra  = get_post_meta($id, '_evento_link_compra', true) ?: '#';
$estados      = get_post_meta($id, '_evento_estados', true);
if (!is_array($estados)) $estados = [];
$imagen       = get_the_post_thumbnail_url($id, 'full') ?: 'https://picsum.photos/seed/' . $id . '/600/800';

// Galería
$galeria_ids = get_post_meta($id, '_evento_galeria', true);
$galeria     = [];
if (is_array($galeria_ids)) {
    foreach ($galeria_ids as $gid) {
        $url = wp_get_attachment_image_url($gid, 'large');
        if ($url) $galeria[] = $url;
    }
}

// Taxonomías
$tipo_terms = get_the_terms($id, 'snow_tipo');
$tipo       = ($tipo_terms && !is_wp_error($tipo_terms)) ? $tipo_terms[0]->name : '';
$pais_terms = get_the_terms($id, 'snow_pais');
$pais       = ($pais_terms && !is_wp_error($pais_terms)) ? $pais_terms[0]->name : '';

$badge_map = snow_get_badge_map();

get_header();
?>

<main class="pdp-main">
  <div class="container">

    <!-- ===================== PDP HERO ===================== -->
    <div class="pdp-hero">

      <!-- Left column: image -->
      <div class="pdp-image-wrap">
        <img
          class="pdp-image"
          src="<?php echo esc_url($imagen); ?>"
          alt="<?php echo esc_attr($titulo); ?>"
          loading="eager"
          fetchpriority="high"
        >
        <div class="pdp-image-overlay" aria-hidden="true"></div>

        <div class="pdp-image-date">
          <?php echo esc_html($fecha_full); ?>
        </div>

        <div class="pdp-image-caption">
          <h2 class="pdp-image-caption__title"><?php echo esc_html($titulo); ?></h2>
          <p class="pdp-image-caption__sub">
            <?php echo esc_html($tipo); ?> &middot; <?php echo esc_html($ciudad); ?>
          </p>
        </div>
      </div>

      <!-- Right column: info -->
      <div class="pdp-info">

        <div>
          <span class="pdp-status-badge">PRÓXIMO SHOW</span>
        </div>

        <h1 class="pdp-title"><?php echo esc_html($titulo); ?></h1>

        <!-- 2×2 data grid -->
        <div class="pdp-data-grid">
          <div class="pdp-data-cell">
            <p class="pdp-data-cell__label">Fecha</p>
            <p class="pdp-data-cell__value"><?php echo esc_html($fecha_full); ?></p>
            <p class="pdp-data-cell__sub"><?php echo esc_html($hora); ?></p>
          </div>
          <div class="pdp-data-cell">
            <p class="pdp-data-cell__label">Lugar</p>
            <p class="pdp-data-cell__value"><?php echo esc_html($lugar); ?></p>
          </div>
          <div class="pdp-data-cell">
            <p class="pdp-data-cell__label">Ciudad</p>
            <p class="pdp-data-cell__value">
              <?php echo esc_html($pais_bandera); ?>
              <?php echo esc_html($ciudad); ?>
            </p>
            <p class="pdp-data-cell__sub"><?php echo esc_html($pais); ?></p>
          </div>
          <div class="pdp-data-cell">
            <p class="pdp-data-cell__label">Talento</p>
            <p class="pdp-data-cell__value">
              <?php if ($talento_data['url']) : ?>
                <a href="<?php echo esc_url($talento_data['url']); ?>" style="color:var(--snow-lime);text-decoration:none;"><?php echo esc_html($talento_data['nombre']); ?></a>
              <?php else : ?>
                <?php echo esc_html($talento_data['nombre']); ?>
              <?php endif; ?>
            </p>
          </div>
        </div>

        <!-- Description -->
        <p class="pdp-desc"><?php echo esc_html($descripcion); ?></p>

        <!-- Countdown -->
        <?php $es_futuro = $fecha_raw && strtotime($fecha_raw) >= strtotime('today'); ?>
        <?php if ($es_futuro) : ?>
        <div class="snow-countdown" id="snow-countdown" data-datetime="<?php echo esc_attr($fecha_raw . 'T' . ($hora ?: '00:00') . ':00'); ?>">
            <div class="snow-countdown__label">Faltan</div>
            <div class="snow-countdown__units">
                <div class="snow-countdown__unit">
                    <span class="snow-countdown__num" id="cd-days">--</span>
                    <span class="snow-countdown__txt">días</span>
                </div>
                <div class="snow-countdown__sep">:</div>
                <div class="snow-countdown__unit">
                    <span class="snow-countdown__num" id="cd-hours">--</span>
                    <span class="snow-countdown__txt">horas</span>
                </div>
                <div class="snow-countdown__sep">:</div>
                <div class="snow-countdown__unit">
                    <span class="snow-countdown__num" id="cd-mins">--</span>
                    <span class="snow-countdown__txt">min</span>
                </div>
                <div class="snow-countdown__sep">:</div>
                <div class="snow-countdown__unit">
                    <span class="snow-countdown__num" id="cd-secs">--</span>
                    <span class="snow-countdown__txt">seg</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- CTA -->
        <div class="pdp-cta">
          <?php if (in_array('sold-out', $estados, true)) : ?>
            <button class="btn btn--disabled" disabled aria-disabled="true">AGOTADO</button>
          <?php else : ?>
            <a
              href="<?php echo esc_url($link_compra); ?>"
              class="btn btn--lime btn--lg"
              target="_blank"
              rel="noopener noreferrer"
            >
              COMPRAR ENTRADA &rarr;
            </a>
          <?php endif; ?>

          <label class="pdp-subscribe">
            <input
              type="checkbox"
              class="snow-suscribir-checkbox"
              name="subscribe-talento"
              value="1"
              data-talento-id="<?php echo esc_attr($talento_data['id']); ?>"
              data-talento-nombre="<?php echo esc_attr($talento_data['nombre']); ?>"
            >
            Suscribirme a alertas de <?php echo esc_html($talento_data['nombre']); ?>
          </label>
        </div>

      </div>
    </div><!-- /.pdp-hero -->

    <!-- ===================== GALLERY ===================== -->
    <?php
    $total_galeria = count($galeria);
    if ($total_galeria > 0) :
      $show_count = min(5, $total_galeria);
      $extras     = $total_galeria - 5;
    ?>
    <section class="gallery-section">
      <h2 class="section-title">GALERÍA DEL SHOW</h2>

      <div class="gallery-grid" id="snow-gallery">
        <?php for ($i = 0; $i < $show_count; $i++) : ?>
        <a
          href="<?php echo esc_url($galeria[$i]); ?>"
          class="gallery-item glightbox"
          data-gallery="snow-gallery-<?php echo esc_attr($id); ?>"
          data-description="<?php echo esc_attr($titulo); ?>"
          style="background-image: url('<?php echo esc_url($galeria[$i]); ?>')"
        ></a>
        <?php endfor; ?>

        <?php if ($extras > 0 && isset($galeria[5])) : ?>
        <div
          class="gallery-item gallery-item--more"
          style="background-image: url('<?php echo esc_url($galeria[5]); ?>')"
          role="button"
          aria-label="Ver <?php echo esc_attr($extras); ?> fotos más"
          tabindex="0"
        >
          <div class="gallery-item__overlay">
            <span class="gallery-more-text">+<?php echo esc_html($extras); ?> más</span>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($total_galeria > 5) : ?>
      <div style="display:none;" aria-hidden="true">
        <?php for ($j = 5; $j < $total_galeria; $j++) : ?>
          <a
            href="<?php echo esc_url($galeria[$j]); ?>"
            class="glightbox"
            data-gallery="snow-gallery-<?php echo esc_attr($id); ?>"
            data-description="<?php echo esc_attr($titulo); ?>"
          ></a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </section>
    <?php endif; ?>

  </div><!-- /.container -->
</main>

<!-- ===================== MODAL SUSCRIPCIÓN ===================== -->
<div id="snow-modal-overlay" class="snow-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
    <div class="snow-modal">
        <button class="snow-modal__close" id="snow-modal-close" aria-label="Cerrar">&#x2715;</button>

        <div class="snow-modal__header">
            <h3 class="snow-modal__title" id="modal-title">
                Alertas de <span id="modal-talento-nombre"></span>
            </h3>
            <p class="snow-modal__subtitle">
                Te avisamos cuando haya nuevos shows disponibles.
            </p>
        </div>

        <div class="snow-modal__body">
            <div class="snow-modal__row">
                <div class="snow-modal__field">
                    <label for="snow-nombre">Nombre</label>
                    <input type="text" id="snow-nombre" placeholder="Tu nombre" autocomplete="given-name">
                </div>
                <div class="snow-modal__field">
                    <label for="snow-apellido">Apellido</label>
                    <input type="text" id="snow-apellido" placeholder="Tu apellido" autocomplete="family-name">
                </div>
            </div>
            <div class="snow-modal__field">
                <label for="snow-email">Email</label>
                <input type="email" id="snow-email" placeholder="tu@email.com" autocomplete="email">
            </div>
            <div id="snow-modal-msg" class="snow-modal__msg" hidden></div>
        </div>

        <div class="snow-modal__footer">
            <button id="snow-modal-submit" class="snow-modal__btn">
                Suscribirme
            </button>
        </div>
    </div>
</div>

<?php get_footer(); ?>
