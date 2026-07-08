<?php
if (!defined('ABSPATH')) exit;

// Habilitar SVG en la biblioteca de medios de WordPress
function snow_allow_svg_upload($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'snow_allow_svg_upload');

// Corregir el tipo MIME de SVG (WP a veces lo detecta mal)
function snow_fix_svg_mime($data, $file, $filename, $mimes) {
    $filetype = wp_check_filetype($filename, $mimes);
    if ($filetype['ext'] === 'svg') {
        $data['ext']  = 'svg';
        $data['type'] = 'image/svg+xml';
    }
    return $data;
}
add_filter('wp_check_filetype_and_ext', 'snow_fix_svg_mime', 10, 4);

// Mostrar SVG como imagen en la biblioteca de medios (preview)
function snow_svg_media_thumbnails($response, $attachment) {
    if ($response['mime'] === 'image/svg+xml') {
        $response['sizes'] = [
            'full' => [
                'url'         => $response['url'],
                'width'       => 1000,
                'height'      => 1000,
                'orientation' => 'landscape',
            ],
        ];
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'snow_svg_media_thumbnails', 10, 2);

// Habilitar también WebP y AVIF por si acaso
function snow_allow_modern_formats($mimes) {
    $mimes['webp'] = 'image/webp';
    $mimes['avif'] = 'image/avif';
    return $mimes;
}
add_filter('upload_mimes', 'snow_allow_modern_formats');
