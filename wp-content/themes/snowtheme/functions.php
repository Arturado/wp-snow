<?php
function snowtheme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);
    register_nav_menus(['main-menu' => __('Menú Principal', 'snowtheme')]);
}
add_action('after_setup_theme', 'snowtheme_setup');

function snowtheme_assets() {
    wp_enqueue_style('snowtheme-style', get_template_directory_uri() . '/assets/css/style.css', [], '1.0');
    wp_enqueue_script('snowtheme-main', get_template_directory_uri() . '/assets/js/main.js', [], '1.0', true);
}
add_action('wp_enqueue_scripts', 'snowtheme_assets');

function snowtheme_pdp_assets() {
    if (!is_singular('evento')) return;

    wp_enqueue_style(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css',
        [],
        '3.3.0'
    );
    wp_enqueue_script(
        'glightbox',
        'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js',
        [],
        '3.3.0',
        true
    );
    wp_enqueue_script(
        'snowtheme-pdp',
        get_template_directory_uri() . '/assets/js/snow-pdp.js',
        ['glightbox'],
        '1.0',
        true
    );
    wp_enqueue_style(
        'snowtheme-pdp',
        get_template_directory_uri() . '/assets/css/snow-pdp.css',
        ['snowtheme-style'],
        '1.0'
    );

    $id        = get_the_ID();
    $fecha_raw = get_post_meta($id, '_evento_fecha', true);
    $hora      = get_post_meta($id, '_evento_hora', true) ?: '00:00';
    $datetime  = $fecha_raw ? $fecha_raw . 'T' . $hora . ':00' : '';

    wp_localize_script('snowtheme-pdp', 'snowPDP', [
        'eventoDatetime' => $datetime,
        'ajaxUrl'        => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('snow_suscribir_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'snowtheme_pdp_assets');

function snowtheme_talento_assets() {
    if (is_singular('talento') || is_post_type_archive('talento')) {
        wp_enqueue_style(
            'snowtheme-talento',
            get_template_directory_uri() . '/assets/css/talento.css',
            ['snowtheme-style'],
            '1.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'snowtheme_talento_assets');
