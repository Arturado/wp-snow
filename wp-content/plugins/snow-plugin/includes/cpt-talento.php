<?php
if (!defined('ABSPATH')) exit;

function snow_registrar_cpt_talento() {
    register_post_type('talento', [
        'labels' => [
            'name'          => 'Talentos',
            'singular_name' => 'Talento',
            'add_new_item'  => 'Añadir Talento',
            'edit_item'     => 'Editar Talento',
            'menu_name'     => 'Talentos',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-microphone',
        'menu_position' => 6,
        'supports'      => ['title', 'thumbnail', 'editor'],
        'rewrite'       => ['slug' => 'talento'],
        'show_in_rest'  => true,
    ]);
}
add_action('init', 'snow_registrar_cpt_talento');
