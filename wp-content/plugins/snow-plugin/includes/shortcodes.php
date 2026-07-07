<?php
if (!defined('ABSPATH')) exit;

function snow_get_badge_map(): array {
    return [
        'nuevo'           => ['label' => 'NUEVO',                 'class' => 'badge--nuevo'],
        'destacado'       => ['label' => 'DESTACADO',             'class' => 'badge--destacado'],
        'ultimos-tickets' => ['label' => 'ÚLTIMOS TICKETS',       'class' => 'badge--ultimos'],
        'sold-out'        => ['label' => 'SOLD OUT',              'class' => 'badge--sold-out'],
        'una-semana'      => ['label' => 'A TAN SOLO UNA SEMANA', 'class' => 'badge--una-semana'],
        'nueva-fecha'     => ['label' => 'NUEVA FECHA AGREGADA',  'class' => 'badge--nueva-fecha'],
    ];
}

function snow_render_badges(array $estados, array $badge_map, int $max = 2): string {
    if (empty($estados)) return '';
    $html  = '<div class="snow-badges">';
    $count = count($estados);
    $show  = array_slice($estados, 0, $max);
    foreach ($show as $estado) {
        if (!isset($badge_map[$estado])) continue;
        $b     = $badge_map[$estado];
        $html .= '<span class="snow-badge ' . esc_attr($b['class']) . '">' . esc_html($b['label']) . '</span>';
    }
    if ($count > $max) {
        $html .= '<span class="snow-badge badge--more">+' . ($count - $max) . '</span>';
    }
    $html .= '</div>';
    return $html;
}

// === [snow_proximos_shows] ===
function snow_shortcode_proximos_shows($atts): string {
    $atts = shortcode_atts([
        'limit' => 6,
        'pais'  => '',
        'tipo'  => '',
    ], $atts, 'snow_proximos_shows');

    $limit = absint($atts['limit']);
    $pais  = sanitize_text_field($atts['pais']);
    $tipo  = sanitize_text_field($atts['tipo']);

    $args = [
        'post_type'      => 'evento',
        'posts_per_page' => $limit,
        'meta_key'       => '_evento_fecha',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => [[
            'key'     => '_evento_fecha',
            'value'   => date('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE',
        ]],
    ];

    $tax_query = [];
    if ($pais) {
        $tax_query[] = [
            'taxonomy' => 'snow_pais',
            'field'    => 'name',
            'terms'    => $pais,
        ];
    }
    if ($tipo) {
        $tax_query[] = [
            'taxonomy' => 'snow_tipo',
            'field'    => 'name',
            'terms'    => $tipo,
        ];
    }
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }

    $query     = new WP_Query($args);
    $badge_map = snow_get_badge_map();

    ob_start();
    ?>
    <div class="eventos-grid" id="eventos-grid">
    <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
        $id           = get_the_ID();
        $titulo       = get_the_title();
        $slug         = get_post_field('post_name', $id);
        $fecha_raw    = get_post_meta($id, '_evento_fecha', true);
        $fecha        = $fecha_raw ? strtoupper(date_i18n('d M', strtotime($fecha_raw))) : '';
        $lugar        = get_post_meta($id, '_evento_lugar', true);
        $ciudad       = get_post_meta($id, '_evento_ciudad', true);
        $pais_bandera = get_post_meta($id, '_evento_pais_bandera', true);
        $talento      = get_post_meta($id, '_evento_talento', true);
        $estados      = get_post_meta($id, '_evento_estados', true);
        if (!is_array($estados)) $estados = [];
        $imagen       = get_the_post_thumbnail_url($id, 'large') ?: 'https://picsum.photos/seed/' . $id . '/600/800';
        $link_compra  = get_post_meta($id, '_evento_link_compra', true) ?: '#';
        $tipo_terms   = get_the_terms($id, 'snow_tipo');
        $tipo_val     = ($tipo_terms && !is_wp_error($tipo_terms)) ? $tipo_terms[0]->name : '';
        $pais_terms   = get_the_terms($id, 'snow_pais');
        $pais_val     = ($pais_terms && !is_wp_error($pais_terms)) ? $pais_terms[0]->name : '';
    ?>
        <article
          class="evento-card"
          data-pais="<?php echo esc_attr($pais_val); ?>"
          data-tipo="<?php echo esc_attr($tipo_val); ?>"
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
              <div class="evento-card__date"><?php echo esc_html($fecha); ?></div>

              <?php echo snow_render_badges($estados, $badge_map); ?>

              <div class="evento-card__overlay">
                <div class="evento-card__info">
                  <h3 class="evento-card__title"><?php echo esc_html($titulo); ?></h3>
                  <p class="evento-card__subtitle"><?php echo esc_html($tipo_val); ?> &middot; <?php echo esc_html($ciudad); ?></p>
                  <div class="evento-card__venue">
                    <span><?php echo esc_html($pais_bandera); ?> <?php echo esc_html($ciudad); ?>, <?php echo esc_html($pais_val); ?></span>
                    <span>📍 <?php echo esc_html($lugar); ?></span>
                  </div>
                  <div class="evento-card__talento"><?php echo esc_html($talento); ?></div>

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
    <?php endwhile; wp_reset_postdata();
    else : ?>
        <div class="snow-empty-state">
            <p>Próximamente se anunciarán nuevos shows. ¡Volvé pronto!</p>
        </div>
    <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('snow_proximos_shows', 'snow_shortcode_proximos_shows');

// === [snow_portfolio] ===
function snow_shortcode_portfolio($atts): string {
    $atts = shortcode_atts([
        'limit'       => 6,
        'mostrar_mas' => 'true',
    ], $atts, 'snow_portfolio');

    $limit      = absint($atts['limit']);
    $mostrar_mas = filter_var($atts['mostrar_mas'], FILTER_VALIDATE_BOOLEAN);

    $query = new WP_Query([
        'post_type'      => 'evento',
        'posts_per_page' => -1,
        'meta_key'       => '_evento_fecha',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
        'meta_query'     => [[
            'key'     => '_evento_fecha',
            'value'   => date('Y-m-d'),
            'compare' => '<',
            'type'    => 'DATE',
        ]],
    ]);

    $total      = $query->found_posts;
    $portfolio_extra = max(0, $total - $limit);

    ob_start();
    ?>
    <div class="portfolio-grid" id="portfolio-grid">
    <?php $index = 0; while ($query->have_posts()) : $query->the_post();
        $id         = get_the_ID();
        $titulo     = get_the_title();
        $ciudad     = get_post_meta($id, '_evento_ciudad', true);
        $pais_terms = get_the_terms($id, 'snow_pais');
        $pais       = ($pais_terms && !is_wp_error($pais_terms)) ? $pais_terms[0]->slug : '';
        $fecha_raw  = get_post_meta($id, '_evento_fecha', true);
        $año        = $fecha_raw ? date('Y', strtotime($fecha_raw)) : '';
        $tipo_terms = get_the_terms($id, 'snow_tipo');
        $tipo       = ($tipo_terms && !is_wp_error($tipo_terms)) ? $tipo_terms[0]->name : '';
        $imagen     = get_the_post_thumbnail_url($id, 'large') ?: 'https://picsum.photos/seed/port' . $id . '/600/400';
        $hidden     = $index >= $limit ? ' portfolio-card--hidden' : '';
    ?>
        <div
          class="portfolio-card<?php echo esc_attr($hidden); ?>"
          style="background-image: url('<?php echo esc_url($imagen); ?>')"
          role="img"
          aria-label="<?php echo esc_attr($titulo); ?>"
        >
          <div class="portfolio-card__overlay">
            <span class="portfolio-card__tipo"><?php echo esc_html($tipo); ?></span>
            <h3 class="portfolio-card__title"><?php echo esc_html($titulo); ?></h3>
            <p class="portfolio-card__meta">
              <?php echo esc_html($ciudad); ?>, <?php echo esc_html($pais); ?>
              &middot; <?php echo esc_html($año); ?>
            </p>
          </div>
        </div>
    <?php $index++; endwhile; wp_reset_postdata(); ?>
    </div>

    <?php if ($mostrar_mas && $portfolio_extra > 0) : ?>
    <div class="portfolio-loadmore-wrap" id="portfolio-loadmore-wrap">
        <button id="portfolio-loadmore" class="btn btn--outline">
            Mostrar más
            <span class="btn__counter"><?php echo esc_html($portfolio_extra); ?> shows más</span>
        </button>
    </div>
    <?php endif;

    return ob_get_clean();
}
add_shortcode('snow_portfolio', 'snow_shortcode_portfolio');
