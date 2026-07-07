<?php
if (!defined('ABSPATH')) exit;

function snow_schema_event() {
    if (!is_singular('evento')) return;

    $id          = get_the_ID();
    $titulo      = get_the_title($id);
    $descripcion = get_post_meta($id, '_evento_descripcion', true) ?: get_the_excerpt($id);
    $fecha_raw   = get_post_meta($id, '_evento_fecha', true);
    $hora        = get_post_meta($id, '_evento_hora', true);
    $lugar       = get_post_meta($id, '_evento_lugar', true);
    $ciudad      = get_post_meta($id, '_evento_ciudad', true);
    $link_compra = get_post_meta($id, '_evento_link_compra', true);
    $estados     = get_post_meta($id, '_evento_estados', true);
    if (!is_array($estados)) $estados = [];
    $imagen = get_the_post_thumbnail_url($id, 'large');

    $pais_terms  = get_the_terms($id, 'snow_pais');
    $pais_nombre = ($pais_terms && !is_wp_error($pais_terms)) ? $pais_terms[0]->name : '';

    $talento_data = snow_get_talento_data($id);

    $start_date = '';
    if ($fecha_raw) {
        $hora_clean = $hora ?: '00:00';
        $start_date = $fecha_raw . 'T' . $hora_clean . ':00';
    }

    $availability = in_array('sold-out', $estados)
        ? 'https://schema.org/SoldOut'
        : 'https://schema.org/InStock';

    $schema = [
        '@context'             => 'https://schema.org',
        '@type'                => 'Event',
        'name'                 => $titulo,
        'startDate'            => $start_date,
        'eventStatus'          => 'https://schema.org/EventScheduled',
        'eventAttendanceMode'  => 'https://schema.org/OfflineEventAttendanceMode',
        'location'             => [
            '@type'   => 'Place',
            'name'    => $lugar,
            'address' => [
                '@type'           => 'PostalAddress',
                'addressLocality' => $ciudad,
                'addressCountry'  => $pais_nombre,
            ],
        ],
        'organizer' => [
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'url'   => home_url(),
        ],
    ];

    if ($descripcion)            $schema['description'] = wp_strip_all_tags($descripcion);
    if ($imagen)                 $schema['image']       = $imagen;

    if ($talento_data['nombre']) {
        $schema['performer'] = [
            '@type' => 'Person',
            'name'  => $talento_data['nombre'],
        ];
        if ($talento_data['url']) {
            $schema['performer']['url'] = $talento_data['url'];
        }
    }

    if ($link_compra) {
        $schema['offers'] = [
            '@type'         => 'Offer',
            'url'           => $link_compra,
            'availability'  => $availability,
            'priceCurrency' => 'EUR',
        ];
    }

    echo "\n<script type=\"application/ld+json\">\n";
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
add_action('wp_head', 'snow_schema_event');

function snow_schema_person() {
    if (!is_singular('talento')) return;

    $id      = get_the_ID();
    $nombre  = get_the_title($id);
    $bio     = get_post_field('post_content', $id);
    $foto    = get_the_post_thumbnail_url($id, 'large');
    $pais    = get_post_meta($id, '_talento_pais', true);
    $web     = get_post_meta($id, '_talento_web', true);
    $ig      = get_post_meta($id, '_talento_instagram', true);
    $yt      = get_post_meta($id, '_talento_youtube', true);
    $spotify = get_post_meta($id, '_talento_spotify', true);

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        'name'        => $nombre,
        'nationality' => $pais,
        'url'         => get_permalink($id),
    ];

    if ($bio)     $schema['description'] = wp_strip_all_tags(wp_trim_words($bio, 50));
    if ($foto)    $schema['image']       = $foto;
    if ($web)     $schema['sameAs'][]    = $web;
    if ($ig)      $schema['sameAs'][]    = $ig;
    if ($yt)      $schema['sameAs'][]    = $yt;
    if ($spotify) $schema['sameAs'][]    = $spotify;

    echo "\n<script type=\"application/ld+json\">\n";
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
add_action('wp_head', 'snow_schema_person');
