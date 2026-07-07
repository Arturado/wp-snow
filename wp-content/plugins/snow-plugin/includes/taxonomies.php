<?php
if (!defined('ABSPATH')) exit;

function snow_registrar_taxonomias() {
    register_taxonomy('snow_tipo', 'evento', [
        'labels' => [
            'name'          => 'Tipos de Evento',
            'singular_name' => 'Tipo de Evento',
            'search_items'  => 'Buscar Tipos',
            'all_items'     => 'Todos los Tipos',
            'edit_item'     => 'Editar Tipo',
            'update_item'   => 'Actualizar Tipo',
            'add_new_item'  => 'Añadir Tipo',
            'new_item_name' => 'Nuevo Tipo',
            'menu_name'     => 'Tipos',
        ],
        'hierarchical' => true,
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'tipo-evento'],
    ]);

    register_taxonomy('snow_pais', 'evento', [
        'labels' => [
            'name'          => 'Países',
            'singular_name' => 'País',
            'search_items'  => 'Buscar Países',
            'all_items'     => 'Todos los Países',
            'edit_item'     => 'Editar País',
            'update_item'   => 'Actualizar País',
            'add_new_item'  => 'Añadir País',
            'new_item_name' => 'Nuevo País',
            'menu_name'     => 'Países',
        ],
        'hierarchical' => true,
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'pais-evento'],
    ]);
}
add_action('init', 'snow_registrar_taxonomias');

function snow_insertar_terminos_defecto() {
    snow_registrar_taxonomias();
    $tipos = ['Stand Up', 'Música', 'Festival', 'Conferencia'];
    foreach ($tipos as $tipo) {
        if (!term_exists($tipo, 'snow_tipo')) {
            wp_insert_term($tipo, 'snow_tipo');
        }
    }
}
