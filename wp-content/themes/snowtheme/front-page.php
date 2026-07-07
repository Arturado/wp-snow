<?php
// Próximos shows: eventos futuros ordenados por fecha ASC
$snow_query_proximos = new WP_Query([
    'post_type'      => 'evento',
    'posts_per_page' => -1,
    'meta_key'       => '_evento_fecha',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [[
        'key'     => '_evento_fecha',
        'value'   => date('Y-m-d'),
        'compare' => '>=',
        'type'    => 'DATE',
    ]],
]);
$snow_eventos_posts = $snow_query_proximos->posts;

// Counter stats
$total_eventos = $snow_query_proximos->found_posts;
$pais_nombres  = array_filter(array_unique(array_map(function($p) {
    $terms = get_the_terms($p->ID, 'snow_pais');
    return ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
}, $snow_eventos_posts)));
$count_paises  = count($pais_nombres);

// Filter selects from taxonomy terms
$paises_terms = get_terms(['taxonomy' => 'snow_pais', 'hide_empty' => false]);
$tipos_terms  = get_terms(['taxonomy' => 'snow_tipo', 'hide_empty' => false]);

$badge_map = snow_get_badge_map();

get_header();
?>

<main class="snow-main">

  <!-- ===================== HERO ===================== -->
  <section class="hero" id="inicio">
    <div class="hero__bg" role="img" aria-label="Fondo del hero: show en vivo"></div>
    <div class="hero__overlay" aria-hidden="true"></div>

    <div class="container hero__content">
      <div class="hero__text">
        <h1 class="hero__title">
          <span class="hero__title-line1">CADA SHOW,</span>
          <span class="hero__title-line2">
            <span class="white">UNA</span> <span class="lime">EXPERIENCIA</span>
          </span>
        </h1>
        <p class="hero__desc">
          Productora de eventos en 15 países. Stand Up, Música, Festivales y Conferencias
          para los mejores talentos de habla hispana.
        </p>
        <div class="hero__stats" aria-label="Estadísticas">
          <div class="hero__stat">
            <strong class="hero__stat-number">1.000+</strong>
            <span class="hero__stat-label">Shows producidos</span>
          </div>
          <div class="hero__stat">
            <strong class="hero__stat-number">15</strong>
            <span class="hero__stat-label">Países</span>
          </div>
          <div class="hero__stat">
            <strong class="hero__stat-number">10</strong>
            <span class="hero__stat-label">Años</span>
          </div>
        </div>
      </div>

      <div class="hero__asterisk-wrap" aria-hidden="true">
        <span id="hero-asterisk" class="hero__asterisk">*</span>
      </div>
    </div>
  </section>

  <!-- ===================== FILTER ===================== -->
  <section class="filter-section" id="shows">
    <div class="container">
      <div class="filter-bar">
        <div class="filter-bar__search">
          <input
            type="text"
            id="filter-search"
            class="filter-input"
            placeholder="Buscar talento o show..."
            aria-label="Buscar talento o show"
          >
        </div>
        <div class="filter-bar__selects">
          <select id="filter-pais" class="filter-select" aria-label="Filtrar por país">
            <option value="">Todos los países</option>
            <?php if (!is_wp_error($paises_terms)) foreach ($paises_terms as $term) : ?>
            <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
          </select>
          <select id="filter-tipo" class="filter-select" aria-label="Filtrar por tipo">
            <option value="">Todos los tipos</option>
            <?php if (!is_wp_error($tipos_terms)) foreach ($tipos_terms as $term) : ?>
            <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button id="filter-btn" class="btn btn--lime">BUSCAR</button>
      </div>
      <p class="filter-hint">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <circle cx="7" cy="7" r="6.5" stroke="#999"/>
          <path d="M7 6v4M7 4.5v.5" stroke="#999" stroke-linecap="round"/>
        </svg>
        Ej: busca por ciudad, talento o combina país y tipo de evento
      </p>
    </div>
  </section>

  <!-- ===================== PRÓXIMOS SHOWS ===================== -->
  <section class="eventos-section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">PRÓXIMOS SHOWS</h2>
        <span class="section-counter" id="eventos-counter">
          <?php echo esc_html($total_eventos); ?> eventos
          &middot;
          <?php echo esc_html($count_paises); ?> países
        </span>
      </div>

      <div class="eventos-grid" id="eventos-grid">
        <?php if (!empty($snow_eventos_posts)) :
          foreach ($snow_eventos_posts as $post) :
            setup_postdata($post);
            $id           = $post->ID;
            $titulo       = get_the_title($id);
            $slug         = $post->post_name;
            $fecha_raw    = get_post_meta($id, '_evento_fecha', true);
            $fecha        = $fecha_raw ? strtoupper(date_i18n('d M', strtotime($fecha_raw))) : '';
            $lugar        = get_post_meta($id, '_evento_lugar', true);
            $ciudad       = get_post_meta($id, '_evento_ciudad', true);
            $pais_bandera   = get_post_meta($id, '_evento_pais_bandera', true);
            $talento_nombre = snow_get_talento_nombre($id);
            $talento_id_val = get_post_meta($id, '_evento_talento_id', true);
            $talento_url    = $talento_id_val ? get_permalink($talento_id_val) : '';
            $estados      = get_post_meta($id, '_evento_estados', true);
            if (!is_array($estados)) $estados = [];
            $imagen       = get_the_post_thumbnail_url($id, 'large') ?: 'https://picsum.photos/seed/' . $id . '/600/800';
            $link_compra  = get_post_meta($id, '_evento_link_compra', true) ?: '#';
            $tipo_terms   = get_the_terms($id, 'snow_tipo');
            $tipo         = ($tipo_terms && !is_wp_error($tipo_terms)) ? $tipo_terms[0]->name : '';
            $pais_terms   = get_the_terms($id, 'snow_pais');
            $pais         = ($pais_terms && !is_wp_error($pais_terms)) ? $pais_terms[0]->name : '';
        ?>
        <article
          class="evento-card"
          data-pais="<?php echo esc_attr($pais); ?>"
          data-tipo="<?php echo esc_attr($tipo); ?>"
          data-titulo="<?php echo esc_attr(strtolower($titulo)); ?>"
          data-talento="<?php echo esc_attr(strtolower($talento_nombre)); ?>"
        >
          <a
            href="<?php echo esc_url(home_url('/evento/' . $slug)); ?>"
            class="evento-card__link"
            aria-label="Ver evento: <?php echo esc_attr($titulo); ?>"
          >
            <div
              class="evento-card__image"
              style="background-image: url('<?php echo esc_url($imagen); ?>')"
              role="img"
              aria-label="<?php echo esc_attr($titulo); ?>"
            >
              <!-- Date badge -->
              <div class="evento-card__date"><?php echo esc_html($fecha); ?></div>

              <!-- Status badges -->
              <?php echo snow_render_badges($estados, $badge_map); ?>

              <!-- Bottom overlay -->
              <div class="evento-card__overlay">
                <div class="evento-card__info">
                  <h3 class="evento-card__title"><?php echo esc_html($titulo); ?></h3>
                  <p class="evento-card__subtitle"><?php echo esc_html($tipo); ?> &middot; <?php echo esc_html($ciudad); ?></p>
                  <div class="evento-card__venue">
                    <span><?php echo esc_html($pais_bandera); ?> <?php echo esc_html($ciudad); ?>, <?php echo esc_html($pais); ?></span>
                    <span>📍 <?php echo esc_html($lugar); ?></span>
                  </div>
                  <div class="evento-card__talento">
                    <?php if ($talento_url) : ?>
                      <a href="<?php echo esc_url($talento_url); ?>" style="color:var(--snow-lime);text-decoration:none;"><?php echo esc_html($talento_nombre); ?></a>
                    <?php else : ?>
                      <?php echo esc_html($talento_nombre); ?>
                    <?php endif; ?>
                  </div>

                  <?php if (in_array('sold-out', $estados, true)) : ?>
                    <button class="btn btn--disabled btn--sm" disabled aria-disabled="true">AGOTADO</button>
                  <?php else : ?>
                    <span class="btn btn--lime btn--sm">COMPRAR</span>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </a>
        </article>
        <?php endforeach;
          wp_reset_postdata();
        else : ?>
        <div class="snow-empty-state">
          <p>Próximamente se anunciarán nuevos shows. ¡Volvé pronto!</p>
        </div>
        <?php endif; ?>
      </div>

      <div id="no-results" class="no-results" style="display:none;" aria-live="polite">
        <p>No hay shows que coincidan con tu búsqueda</p>
      </div>
    </div>
  </section>

  <!-- ===================== TALENTOS ===================== -->
  <section class="portfolio-section" id="historial">
    <div class="container">
      <div class="section-header">
        <div>
          <h2 class="section-title">HAN PASADO POR SNOW<span class="text-blue">*</span></h2>
          <p class="section-subtitle">Nuestros talentos</p>
        </div>
      </div>

      <?php
      $talentos_query = new WP_Query([
          'post_type'      => 'talento',
          'posts_per_page' => -1,
          'orderby'        => 'title',
          'order'          => 'ASC',
          'post_status'    => 'publish',
      ]);
      $talentos_posts  = $talentos_query->posts;
      $talento_visible = 6;
      $talento_extra   = max(0, count($talentos_posts) - $talento_visible);
      ?>

      <div class="portfolio-grid" id="portfolio-grid">
        <?php foreach ($talentos_posts as $index => $talento_post) :
            $tid      = $talento_post->ID;
            $tnombre  = $talento_post->post_title;
            $tpais    = get_post_meta($tid, '_talento_pais', true);
            $tbandera = get_post_meta($tid, '_talento_bandera', true);
            $ttipo_terms = get_the_terms($tid, 'snow_tipo');
            $ttipo    = ($ttipo_terms && !is_wp_error($ttipo_terms)) ? $ttipo_terms[0]->name : '';
            $tfoto    = get_the_post_thumbnail_url($tid, 'large')
                        ?: 'https://picsum.photos/seed/talento' . $tid . '/600/400';
            $turl     = get_permalink($tid);
        ?>
        <a href="<?php echo esc_url($turl); ?>"
           class="portfolio-card<?php echo $index >= $talento_visible ? ' portfolio-card--hidden' : ''; ?>"
           style="background-image: url('<?php echo esc_url($tfoto); ?>')"
           aria-label="<?php echo esc_attr($tnombre); ?>"
        >
            <div class="portfolio-card__overlay">
                <?php if ($ttipo) : ?>
                <span class="portfolio-card__tipo"><?php echo esc_html($ttipo); ?></span>
                <?php endif; ?>
                <h3 class="portfolio-card__title"><?php echo esc_html($tnombre); ?></h3>
                <p class="portfolio-card__meta">
                    <?php echo esc_html(trim($tbandera . ' ' . $tpais)); ?>
                </p>
            </div>
        </a>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>

      <?php if ($talento_extra > 0) : ?>
      <div class="portfolio-loadmore-wrap" id="portfolio-loadmore-wrap">
        <button id="portfolio-loadmore" class="btn btn--outline">
          Mostrar más
          <span class="btn__counter"><?php echo esc_html($talento_extra); ?> talentos más</span>
        </button>
      </div>
      <?php endif; ?>

    </div>
  </section>

</main>

<?php get_footer(); ?>
