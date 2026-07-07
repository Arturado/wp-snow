<?php
function snow_registrar_cpt_evento() {
    register_post_type('evento', [
        'labels' => [
            'name' => 'Eventos',
            'singular_name' => 'Evento',
            'add_new_item' => 'Añadir Evento',
            'edit_item' => 'Editar Evento',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => ['title', 'editor', 'thumbnail'],
        'rewrite' => ['slug' => 'eventos'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'snow_registrar_cpt_evento');
