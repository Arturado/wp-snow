<?php
if (!defined('ABSPATH')) exit;

function snow_registrar_cpt_video() {
    register_post_type('video', [
        'labels' => [
            'name'          => 'Videos',
            'singular_name' => 'Video',
            'add_new_item'  => 'Añadir Video',
            'edit_item'     => 'Editar Video',
            'menu_name'     => 'Videos',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-video-alt3',
        'menu_position' => 7,
        'supports'      => ['title', 'thumbnail'],
        'rewrite'       => ['slug' => 'audiovisual'],
        'show_in_rest'  => true,
    ]);
}
add_action('init', 'snow_registrar_cpt_video');

function snow_registrar_tax_video() {
    register_taxonomy('snow_video_cat', 'video', [
        'labels' => [
            'name'          => 'Categorías de Video',
            'singular_name' => 'Categoría',
            'search_items'  => 'Buscar Categorías',
            'all_items'     => 'Todas las Categorías',
            'edit_item'     => 'Editar Categoría',
            'update_item'   => 'Actualizar Categoría',
            'add_new_item'  => 'Añadir Categoría',
            'new_item_name' => 'Nueva Categoría',
            'menu_name'     => 'Categorías',
        ],
        'hierarchical'  => false,
        'public'        => true,
        'rewrite'       => ['slug' => 'video-categoria'],
        'show_in_rest'  => true,
    ]);
}
add_action('init', 'snow_registrar_tax_video');

function snow_insertar_terminos_defecto_video() {
    snow_registrar_tax_video();
    $categorias = ['Stand Up', 'Música', 'Conferencia'];
    foreach ($categorias as $categoria) {
        if (!term_exists($categoria, 'snow_video_cat')) {
            wp_insert_term($categoria, 'snow_video_cat');
        }
    }
}
