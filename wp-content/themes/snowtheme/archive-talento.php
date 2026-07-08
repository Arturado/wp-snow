<?php get_header(); ?>

<main class="snow-main">
  <div class="container">

    <div class="talentos-header">
      <h1 class="talentos-header__title">TALENTOS<span>*</span></h1>
      <p class="talentos-header__sub">HAN CONFIADO EN SNOW *</p>
    </div>

    <div class="talentos-grid">
      <?php if (have_posts()) : while (have_posts()) : the_post();
        $id      = get_the_ID();
        $nombre  = get_the_title();
        $url     = get_permalink();
        $foto    = get_the_post_thumbnail_url($id, 'large');
        $pais    = get_post_meta($id, '_talento_pais', true);
        $bandera = get_post_meta($id, '_talento_bandera', true);
      ?>
        <a href="<?php echo esc_url($url); ?>" class="talento-card">
          <?php if ($foto) : ?>
            <img
              class="talento-card__image"
              src="<?php echo esc_url($foto); ?>"
              alt="<?php echo esc_attr($nombre); ?>"
              loading="lazy"
              decoding="async"
            >
          <?php endif; ?>
          <div class="talento-card__overlay">
            <h2 class="talento-card__nombre"><?php echo esc_html($nombre); ?></h2>
            <?php if ($pais || $bandera) : ?>
              <p class="talento-card__pais">
                <?php echo esc_html(trim($bandera . ' ' . $pais)); ?>
              </p>
            <?php endif; ?>
          </div>
        </a>
      <?php endwhile;
      else : ?>
        <div class="talentos-empty">
          <p>Próximamente presentaremos a nuestros artistas.</p>
        </div>
      <?php endif; ?>
    </div>

    <?php the_posts_pagination([
      'mid_size'  => 2,
      'prev_text' => '&larr; Anterior',
      'next_text' => 'Siguiente &rarr;',
    ]); ?>

  </div>
</main>

<?php get_footer(); ?>
