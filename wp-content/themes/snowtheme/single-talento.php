<?php
if (!have_posts()) {
    wp_redirect(home_url('/404'));
    exit;
}
the_post();

$talento_id = get_the_ID();
$nombre     = get_the_title();
$pais       = get_post_meta($talento_id, '_talento_pais', true);
$bandera    = get_post_meta($talento_id, '_talento_bandera', true);
$instagram  = get_post_meta($talento_id, '_talento_instagram', true);
$youtube    = get_post_meta($talento_id, '_talento_youtube', true);
$spotify    = get_post_meta($talento_id, '_talento_spotify', true);
$tiktok     = get_post_meta($talento_id, '_talento_tiktok', true);
$web        = get_post_meta($talento_id, '_talento_web', true);
$foto       = get_the_post_thumbnail_url($talento_id, 'large');

$hoy = date('Y-m-d');

$query_proximos = new WP_Query([
    'post_type'      => 'evento',
    'posts_per_page' => -1,
    'meta_query'     => [
        'relation'        => 'AND',
        'talento_clause'  => [
            'key'     => '_evento_talento_id',
            'value'   => $talento_id,
            'compare' => '=',
            'type'    => 'NUMERIC',
        ],
        'fecha_clause'    => [
            'key'     => '_evento_fecha',
            'value'   => $hoy,
            'compare' => '>=',
            'type'    => 'DATE',
        ],
    ],
    'orderby'        => ['fecha_clause' => 'ASC'],
]);

$query_historial = new WP_Query([
    'post_type'      => 'evento',
    'posts_per_page' => -1,
    'meta_query'     => [
        'relation'       => 'AND',
        'talento_clause' => [
            'key'     => '_evento_talento_id',
            'value'   => $talento_id,
            'compare' => '=',
            'type'    => 'NUMERIC',
        ],
        'fecha_clause'   => [
            'key'     => '_evento_fecha',
            'value'   => $hoy,
            'compare' => '<',
            'type'    => 'DATE',
        ],
    ],
    'orderby'        => ['fecha_clause' => 'DESC'],
]);

$badge_map = snow_get_badge_map();

get_header();
?>

<main class="talento-main">
  <div class="container">

    <a href="<?php echo esc_url(get_post_type_archive_link('talento')); ?>" class="talento-back">
      &larr; Ver todos los talentos
    </a>

    <!-- ===================== HERO ===================== -->
    <div class="talento-hero">

      <!-- Columna foto -->
      <div class="talento-hero__photo-wrap">
        <?php if ($foto) : ?>
          <img
            class="talento-hero__photo"
            src="<?php echo esc_url($foto); ?>"
            alt="<?php echo esc_attr($nombre); ?>"
            loading="eager"
            fetchpriority="high"
          >
          <div class="talento-hero__photo-gradient" aria-hidden="true"></div>
        <?php else : ?>
          <div class="talento-hero__photo-placeholder" aria-hidden="true">🎤</div>
        <?php endif; ?>
      </div>

      <!-- Columna info -->
      <div class="talento-hero__info">

        <?php if ($pais || $bandera) : ?>
          <p class="talento-hero__pais">
            <?php echo esc_html(trim($bandera . ' ' . $pais)); ?>
          </p>
        <?php endif; ?>

        <h1 class="talento-hero__nombre"><?php echo esc_html($nombre); ?></h1>
        <hr class="talento-hero__divider">

        <div class="talento-hero__bio">
          <?php the_content(); ?>
        </div>

        <!-- Redes sociales -->
        <?php if ($instagram || $youtube || $spotify || $tiktok || $web) : ?>
        <div class="talento-social">
          <?php if ($instagram) : ?>
            <a href="<?php echo esc_url($instagram); ?>" class="talento-social__link" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($youtube) : ?>
            <a href="<?php echo esc_url($youtube); ?>" class="talento-social__link" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($spotify) : ?>
            <a href="<?php echo esc_url($spotify); ?>" class="talento-social__link" target="_blank" rel="noopener noreferrer" aria-label="Spotify">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($tiktok) : ?>
            <a href="<?php echo esc_url($tiktok); ?>" class="talento-social__link" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34l.02-8.5a8.18 8.18 0 004.82 1.56V4.93a4.85 4.85 0 01-1.07-.24z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($web) : ?>
            <a href="<?php echo esc_url($web); ?>" class="talento-social__link" target="_blank" rel="noopener noreferrer" aria-label="Web oficial">
              <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.22.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      </div>
    </div><!-- /.talento-hero -->

    <!-- ===================== PRÓXIMOS SHOWS ===================== -->
    <section class="talento-section">
      <h2 class="talento-section__title">
        PRÓXIMOS SHOWS DE <span><?php echo esc_html(strtoupper($nombre)); ?></span>
      </h2>

      <?php if ($query_proximos->have_posts()) : ?>
        <div class="eventos-grid">
          <?php while ($query_proximos->have_posts()) : $query_proximos->the_post();
            $eid          = get_the_ID();
            $e_titulo     = get_the_title();
            $e_slug       = get_post_field('post_name', $eid);
            $e_fecha_raw  = get_post_meta($eid, '_evento_fecha', true);
            $e_fecha      = $e_fecha_raw ? strtoupper(date_i18n('d M', strtotime($e_fecha_raw))) : '';
            $e_lugar      = get_post_meta($eid, '_evento_lugar', true);
            $e_ciudad     = get_post_meta($eid, '_evento_ciudad', true);
            $e_pais_flag  = get_post_meta($eid, '_evento_pais_bandera', true);
            $e_estados    = get_post_meta($eid, '_evento_estados', true);
            if (!is_array($e_estados)) $e_estados = [];
            $e_imagen     = get_the_post_thumbnail_url($eid, 'large') ?: 'https://picsum.photos/seed/' . $eid . '/600/800';
            $e_tipo_terms = get_the_terms($eid, 'snow_tipo');
            $e_tipo       = ($e_tipo_terms && !is_wp_error($e_tipo_terms)) ? $e_tipo_terms[0]->name : '';
            $e_pais_terms = get_the_terms($eid, 'snow_pais');
            $e_pais       = ($e_pais_terms && !is_wp_error($e_pais_terms)) ? $e_pais_terms[0]->name : '';
          ?>
            <article
              class="evento-card"
              data-pais="<?php echo esc_attr($e_pais); ?>"
              data-tipo="<?php echo esc_attr($e_tipo); ?>"
            >
              <a
                href="<?php echo esc_url(home_url('/evento/' . $e_slug)); ?>"
                class="evento-card__link"
                aria-label="Ver evento: <?php echo esc_attr($e_titulo); ?>"
              >
                <div
                  class="evento-card__image"
                  style="background-image: url('<?php echo esc_url($e_imagen); ?>')"
                  role="img"
                  aria-label="<?php echo esc_attr($e_titulo); ?>"
                >
                  <div class="evento-card__date"><?php echo esc_html($e_fecha); ?></div>
                  <?php echo snow_render_badges($e_estados, $badge_map); ?>
                  <div class="evento-card__overlay">
                    <div class="evento-card__info">
                      <h3 class="evento-card__title"><?php echo esc_html($e_titulo); ?></h3>
                      <p class="evento-card__subtitle"><?php echo esc_html($e_tipo); ?> &middot; <?php echo esc_html($e_ciudad); ?></p>
                      <div class="evento-card__venue">
                        <span><?php echo esc_html($e_pais_flag); ?> <?php echo esc_html($e_ciudad); ?>, <?php echo esc_html($e_pais); ?></span>
                        <span>📍 <?php echo esc_html($e_lugar); ?></span>
                      </div>
                      <div class="evento-card__talento"><?php echo esc_html($nombre); ?></div>
                      <?php if (in_array('sold-out', $e_estados, true)) : ?>
                        <button class="btn btn--disabled btn--sm" disabled aria-disabled="true">AGOTADO</button>
                      <?php else : ?>
                        <span class="btn btn--lime btn--sm">COMPRAR</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </a>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      <?php else : ?>
        <p class="talento-section__empty">No hay shows próximos para este artista.</p>
      <?php endif; ?>
    </section>

    <!-- ===================== HISTORIAL ===================== -->
    <?php if ($query_historial->have_posts()) : ?>
    <section class="talento-section">
      <h2 class="talento-section__title">
        HISTORIAL DE <span><?php echo esc_html(strtoupper($nombre)); ?></span>
      </h2>

      <div class="portfolio-grid">
        <?php while ($query_historial->have_posts()) : $query_historial->the_post();
          $hid         = get_the_ID();
          $h_titulo    = get_the_title();
          $h_ciudad    = get_post_meta($hid, '_evento_ciudad', true);
          $h_pais_t    = get_the_terms($hid, 'snow_pais');
          $h_pais      = ($h_pais_t && !is_wp_error($h_pais_t)) ? $h_pais_t[0]->slug : '';
          $h_fecha_raw = get_post_meta($hid, '_evento_fecha', true);
          $h_año       = $h_fecha_raw ? date('Y', strtotime($h_fecha_raw)) : '';
          $h_tipo_t    = get_the_terms($hid, 'snow_tipo');
          $h_tipo      = ($h_tipo_t && !is_wp_error($h_tipo_t)) ? $h_tipo_t[0]->name : '';
          $h_imagen    = get_the_post_thumbnail_url($hid, 'large') ?: 'https://picsum.photos/seed/port' . $hid . '/600/400';
        ?>
          <div
            class="portfolio-card"
            style="background-image: url('<?php echo esc_url($h_imagen); ?>')"
            role="img"
            aria-label="<?php echo esc_attr($h_titulo); ?>"
          >
            <div class="portfolio-card__overlay">
              <span class="portfolio-card__tipo"><?php echo esc_html($h_tipo); ?></span>
              <h3 class="portfolio-card__title"><?php echo esc_html($h_titulo); ?></h3>
              <p class="portfolio-card__meta">
                <?php echo esc_html($h_ciudad); ?>, <?php echo esc_html($h_pais); ?>
                &middot; <?php echo esc_html($h_año); ?>
              </p>
            </div>
          </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
