<?php get_header(); ?>

<main class="snow-main">
  <div class="container">

    <div class="talentos-header">
      <h1 class="talentos-header__title">TALENTOS<span>*</span></h1>
      <p class="talentos-header__sub">HAN CONFIADO EN SNOW *</p>
    </div>

    <div class="talentos-grid">
      <?php
      // Talentos con orden asignado, ordenados numéricamente ASC
      $talentos_con_orden = new WP_Query([
          'post_type'      => 'talento',
          'posts_per_page' => -1,
          'post_status'    => 'publish',
          'meta_key'       => '_talento_orden',
          'orderby'        => 'meta_value_num',
          'order'          => 'ASC',
      ]);
      // Talentos sin orden asignado (van al final)
      $talentos_sin_orden = new WP_Query([
          'post_type'      => 'talento',
          'posts_per_page' => -1,
          'post_status'    => 'publish',
          'meta_query'     => [[
              'key'     => '_talento_orden',
              'compare' => 'NOT EXISTS',
          ]],
          'orderby'        => 'title',
          'order'          => 'ASC',
      ]);
      $talentos_posts = array_merge($talentos_con_orden->posts, $talentos_sin_orden->posts);

      if (!empty($talentos_posts)) :
        foreach ($talentos_posts as $talento_post) :
        $id      = $talento_post->ID;
        $nombre  = $talento_post->post_title;
        $url     = get_permalink($id);
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
      <?php endforeach;
      else : ?>
        <div class="talentos-empty">
          <p>Próximamente presentaremos a nuestros artistas.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php get_footer(); ?>
