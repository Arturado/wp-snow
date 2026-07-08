<?php
if (!defined('ABSPATH')) exit;

// Cargar admin.css también en las pantallas de listado
function snow_enqueue_list_styles($hook) {
    if ($hook === 'edit.php' && isset($_GET['post_type']) && in_array($_GET['post_type'], ['evento', 'talento'], true)) {
        wp_enqueue_style('snow-admin-css', SNOW_PLUGIN_URL . 'assets/css/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'snow_enqueue_list_styles');

// ===================== CPT EVENTO =====================

function snow_evento_columns($columns) {
    return [
        'cb'           => $columns['cb'],
        'snow_imagen'  => 'Imagen',
        'title'        => $columns['title'],
        'snow_fecha'   => 'Fecha del show',
        'snow_ciudad'  => 'Ciudad',
        'snow_talento' => 'Talento',
        'snow_tipo'    => 'Tipo',
        'snow_estados' => 'Estado',
    ];
}
add_filter('manage_evento_posts_columns', 'snow_evento_columns');

function snow_evento_column_content($column, $post_id) {
    switch ($column) {

        case 'snow_imagen':
            $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
            if ($thumb) {
                echo '<img class="snow-col-thumb" src="' . esc_url($thumb) . '" alt="">';
            } else {
                echo '<div style="width:60px;height:60px;background:#2c3338;border-radius:4px;"></div>';
            }
            break;

        case 'snow_fecha':
            $fecha_raw = get_post_meta($post_id, '_evento_fecha', true);
            echo $fecha_raw ? esc_html(date_i18n('d M Y', strtotime($fecha_raw))) : '—';
            break;

        case 'snow_ciudad':
            $ciudad  = get_post_meta($post_id, '_evento_ciudad', true);
            $bandera = get_post_meta($post_id, '_evento_pais_bandera', true);
            $texto   = trim($bandera . ' ' . $ciudad);
            echo $texto ? esc_html($texto) : '—';
            break;

        case 'snow_talento':
            $talento_id = get_post_meta($post_id, '_evento_talento_id', true);
            if ($talento_id) {
                $nombre    = get_the_title($talento_id);
                $edit_link = get_edit_post_link($talento_id);
                echo '<a href="' . esc_url($edit_link) . '">' . esc_html($nombre) . '</a>';
            } else {
                $legacy = get_post_meta($post_id, '_evento_talento', true);
                echo $legacy ? esc_html($legacy) : '—';
            }
            break;

        case 'snow_tipo':
            $terms = get_the_terms($post_id, 'snow_tipo');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));
            } else {
                echo '—';
            }
            break;

        case 'snow_estados':
            $estados = get_post_meta($post_id, '_evento_estados', true);
            if (!is_array($estados) || empty($estados)) {
                echo '—';
                break;
            }
            $badge_styles = [
                'nuevo'           => 'background:#b2d430;color:#1a3a00;',
                'destacado'       => 'background:#f0c40d;color:#3a2000;',
                'ultimos-tickets' => 'background:#e05c2a;color:#fff;',
                'sold-out'        => 'background:#d63638;color:#fff;',
                'una-semana'      => 'background:#5b4fcf;color:#fff;',
                'nueva-fecha'     => 'background:#00a0d2;color:#fff;',
            ];
            $labels = [
                'nuevo'           => 'Nuevo',
                'destacado'       => 'Dest.',
                'ultimos-tickets' => 'Últ. Tickets',
                'sold-out'        => 'Sold Out',
                'una-semana'      => '1 Semana',
                'nueva-fecha'     => 'Nueva Fecha',
            ];
            $base = 'display:inline-block;padding:2px 6px;border-radius:3px;font-size:11px;font-weight:600;margin:1px 2px 1px 0;line-height:1.4;white-space:nowrap;';
            foreach ($estados as $estado) {
                $style = $base . ($badge_styles[$estado] ?? 'background:#999;color:#fff;');
                $label = $labels[$estado] ?? $estado;
                echo '<span style="' . esc_attr($style) . '">' . esc_html($label) . '</span>';
            }
            break;
    }
}
add_action('manage_evento_posts_custom_column', 'snow_evento_column_content', 10, 2);

function snow_evento_sortable_columns($columns) {
    $columns['snow_fecha'] = 'evento_fecha';
    return $columns;
}
add_filter('manage_edit-evento_sortable_columns', 'snow_evento_sortable_columns');

function snow_evento_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('orderby') === 'evento_fecha') {
        $query->set('meta_key', '_evento_fecha');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'snow_evento_orderby');

// ===================== CPT TALENTO =====================

function snow_talento_columns($columns) {
    return [
        'cb'                => $columns['cb'],
        'snow_foto'         => 'Foto',
        'title'             => $columns['title'],
        'snow_pais_talento' => 'País',
        'snow_proximos'     => 'Shows próximos',
        'snow_total_shows'  => 'Shows totales',
        'snow_redes'        => 'Redes',
    ];
}
add_filter('manage_talento_posts_columns', 'snow_talento_columns');

function snow_talento_column_content($column, $post_id) {
    switch ($column) {

        case 'snow_foto':
            $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
            if ($thumb) {
                echo '<img class="snow-col-thumb snow-col-thumb--round" src="' . esc_url($thumb) . '" alt="">';
            } else {
                echo '<div style="width:60px;height:60px;background:#2c3338;border-radius:50%;"></div>';
            }
            break;

        case 'snow_pais_talento':
            $pais    = get_post_meta($post_id, '_talento_pais', true);
            $bandera = get_post_meta($post_id, '_talento_bandera', true);
            $texto   = trim($bandera . ' ' . $pais);
            echo $texto ? esc_html($texto) : '—';
            break;

        case 'snow_proximos':
            $count = count(get_posts([
                'post_type'      => 'evento',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => [
                    'relation' => 'AND',
                    [
                        'key'     => '_evento_talento_id',
                        'value'   => $post_id,
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ],
                    [
                        'key'     => '_evento_fecha',
                        'value'   => date('Y-m-d'),
                        'compare' => '>=',
                        'type'    => 'DATE',
                    ],
                ],
            ]));
            $class = $count > 0 ? 'snow-count-badge snow-count-badge--lime' : 'snow-count-badge';
            echo '<span class="' . esc_attr($class) . '">' . esc_html($count) . '</span>';
            break;

        case 'snow_total_shows':
            $count = count(get_posts([
                'post_type'      => 'evento',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => [[
                    'key'     => '_evento_talento_id',
                    'value'   => $post_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ]],
            ]));
            echo '<span class="snow-count-badge">' . esc_html($count) . '</span>';
            break;

        case 'snow_redes':
            $redes = [
                'instagram' => [
                    get_post_meta($post_id, '_talento_instagram', true),
                    'Instagram',
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                ],
                'youtube' => [
                    get_post_meta($post_id, '_talento_youtube', true),
                    'YouTube',
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z"/></svg>',
                ],
                'spotify' => [
                    get_post_meta($post_id, '_talento_spotify', true),
                    'Spotify',
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>',
                ],
                'tiktok' => [
                    get_post_meta($post_id, '_talento_tiktok', true),
                    'TikTok',
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34l.02-8.5a8.18 8.18 0 004.82 1.56V4.93a4.85 4.85 0 01-1.07-.24z"/></svg>',
                ],
                'web' => [
                    get_post_meta($post_id, '_talento_web', true),
                    'Web',
                    '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.22.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
                ],
            ];
            $output = '';
            foreach ($redes as [$url, $label, $svg]) {
                if ($url) {
                    $output .= '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" title="' . esc_attr($label) . '" style="display:inline-flex;align-items:center;margin-right:4px;color:#50575e;vertical-align:middle;">' . $svg . '</a>';
                }
            }
            echo $output ?: '—';
            break;
    }
}
add_action('manage_talento_posts_custom_column', 'snow_talento_column_content', 10, 2);
