<?php
/**
 * Plugin Name: Snow Plugin
 * Description: Gestión de eventos con CPT y metadatos para snow.arturodev.info
 * Version: 2.0
 * Author: Arturado
 * Text Domain: snow-plugin
 */

if (!defined('ABSPATH')) exit;

define('SNOW_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SNOW_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once SNOW_PLUGIN_PATH . 'includes/helpers.php';
require_once SNOW_PLUGIN_PATH . 'includes/cpt-eventos.php';
require_once SNOW_PLUGIN_PATH . 'includes/cpt-talento.php';
require_once SNOW_PLUGIN_PATH . 'includes/taxonomies.php';
require_once SNOW_PLUGIN_PATH . 'includes/metaboxes.php';
require_once SNOW_PLUGIN_PATH . 'includes/metaboxes-talento.php';
require_once SNOW_PLUGIN_PATH . 'includes/shortcodes.php';
require_once SNOW_PLUGIN_PATH . 'includes/admin-columns.php';
require_once SNOW_PLUGIN_PATH . 'includes/schema.php';
require_once SNOW_PLUGIN_PATH . 'includes/suscriptores.php';

register_activation_hook(__FILE__, 'snow_plugin_activar');
function snow_plugin_activar() {
    snow_registrar_cpt_evento();
    snow_registrar_cpt_talento();
    snow_registrar_taxonomias();
    snow_insertar_terminos_defecto();
    snow_crear_tabla_suscriptores();
    flush_rewrite_rules();
}
