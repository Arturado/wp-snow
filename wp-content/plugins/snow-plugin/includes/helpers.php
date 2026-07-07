<?php
if (!defined('ABSPATH')) exit;

function snow_get_paises() {
    return [
        'AR' => ['nombre' => 'Argentina',       'bandera' => '🇦🇷'],
        'BO' => ['nombre' => 'Bolivia',          'bandera' => '🇧🇴'],
        'BR' => ['nombre' => 'Brasil',           'bandera' => '🇧🇷'],
        'CL' => ['nombre' => 'Chile',            'bandera' => '🇨🇱'],
        'CO' => ['nombre' => 'Colombia',         'bandera' => '🇨🇴'],
        'CR' => ['nombre' => 'Costa Rica',       'bandera' => '🇨🇷'],
        'CU' => ['nombre' => 'Cuba',             'bandera' => '🇨🇺'],
        'DO' => ['nombre' => 'Rep. Dominicana',  'bandera' => '🇩🇴'],
        'EC' => ['nombre' => 'Ecuador',          'bandera' => '🇪🇨'],
        'SV' => ['nombre' => 'El Salvador',      'bandera' => '🇸🇻'],
        'ES' => ['nombre' => 'España',           'bandera' => '🇪🇸'],
        'GT' => ['nombre' => 'Guatemala',        'bandera' => '🇬🇹'],
        'HN' => ['nombre' => 'Honduras',         'bandera' => '🇭🇳'],
        'MX' => ['nombre' => 'México',           'bandera' => '🇲🇽'],
        'NI' => ['nombre' => 'Nicaragua',        'bandera' => '🇳🇮'],
        'PA' => ['nombre' => 'Panamá',           'bandera' => '🇵🇦'],
        'PY' => ['nombre' => 'Paraguay',         'bandera' => '🇵🇾'],
        'PE' => ['nombre' => 'Perú',             'bandera' => '🇵🇪'],
        'PR' => ['nombre' => 'Puerto Rico',      'bandera' => '🇵🇷'],
        'UY' => ['nombre' => 'Uruguay',          'bandera' => '🇺🇾'],
        'VE' => ['nombre' => 'Venezuela',        'bandera' => '🇻🇪'],
        'US' => ['nombre' => 'Estados Unidos',   'bandera' => '🇺🇸'],
        'DE' => ['nombre' => 'Alemania',         'bandera' => '🇩🇪'],
        'FR' => ['nombre' => 'Francia',          'bandera' => '🇫🇷'],
        'GB' => ['nombre' => 'Reino Unido',      'bandera' => '🇬🇧'],
        'IT' => ['nombre' => 'Italia',           'bandera' => '🇮🇹'],
        'PT' => ['nombre' => 'Portugal',         'bandera' => '🇵🇹'],
        'NL' => ['nombre' => 'Países Bajos',     'bandera' => '🇳🇱'],
        'CH' => ['nombre' => 'Suiza',            'bandera' => '🇨🇭'],
        'CA' => ['nombre' => 'Canadá',           'bandera' => '🇨🇦'],
        'AU' => ['nombre' => 'Australia',        'bandera' => '🇦🇺'],
        'JP' => ['nombre' => 'Japón',            'bandera' => '🇯🇵'],
    ];
}

function snow_get_talento_nombre($evento_id) {
    $talento_id = get_post_meta($evento_id, '_evento_talento_id', true);
    if ($talento_id) {
        return get_the_title($talento_id);
    }
    return get_post_meta($evento_id, '_evento_talento', true) ?: '';
}

function snow_get_talento_data($evento_id) {
    $talento_id = get_post_meta($evento_id, '_evento_talento_id', true);
    if (!$talento_id) {
        return [
            'id'        => 0,
            'nombre'    => get_post_meta($evento_id, '_evento_talento', true) ?: '',
            'slug'      => '',
            'url'       => '',
            'foto'      => '',
            'pais'      => '',
            'bandera'   => '',
            'instagram' => '',
            'youtube'   => '',
            'spotify'   => '',
            'tiktok'    => '',
            'web'       => '',
        ];
    }
    return [
        'id'        => $talento_id,
        'nombre'    => get_the_title($talento_id),
        'slug'      => get_post_field('post_name', $talento_id),
        'url'       => get_permalink($talento_id),
        'foto'      => get_the_post_thumbnail_url($talento_id, 'large') ?: '',
        'pais'      => get_post_meta($talento_id, '_talento_pais', true),
        'bandera'   => get_post_meta($talento_id, '_talento_bandera', true),
        'instagram' => get_post_meta($talento_id, '_talento_instagram', true),
        'youtube'   => get_post_meta($talento_id, '_talento_youtube', true),
        'spotify'   => get_post_meta($talento_id, '_talento_spotify', true),
        'tiktok'    => get_post_meta($talento_id, '_talento_tiktok', true),
        'web'       => get_post_meta($talento_id, '_talento_web', true),
    ];
}
