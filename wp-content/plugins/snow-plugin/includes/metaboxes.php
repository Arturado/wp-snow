<?php
if (!defined('ABSPATH')) exit;

function snow_enqueue_admin_scripts($hook) {
    global $post;
    if (($hook === 'post.php' || $hook === 'post-new.php') && isset($post) && in_array($post->post_type, ['evento', 'talento'], true)) {
        wp_enqueue_media();
        wp_enqueue_style('snow-admin-css', SNOW_PLUGIN_URL . 'assets/css/admin.css');
    }
}
add_action('admin_enqueue_scripts', 'snow_enqueue_admin_scripts');

// === Register metaboxes ===
function snow_agregar_metaboxes() {
    add_meta_box('snow_detalles', 'Detalles del Show',  'snow_render_metabox_detalles', 'evento', 'normal', 'high');
    add_meta_box('snow_estados',  'Estado del Show',    'snow_render_metabox_estados',  'evento', 'side',   'default');
    add_meta_box('snow_galeria',  'Galería del Show',   'snow_render_metabox_galeria',  'evento', 'normal', 'default');
}
add_action('add_meta_boxes', 'snow_agregar_metaboxes');

// === Metabox 1: Detalles del Show ===
function snow_render_metabox_detalles($post) {
    $id = $post->ID;
    wp_nonce_field('snow_guardar_detalles', 'snow_detalles_nonce');
    ?>
    <table class="snow-meta-table">
        <tr>
            <th><label for="evento_subtitulo">Subtítulo / Tour</label></th>
            <td><input type="text" id="evento_subtitulo" name="evento_subtitulo" value="<?php echo esc_attr(get_post_meta($id, '_evento_subtitulo', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="evento_fecha">Fecha</label></th>
            <td><input type="date" id="evento_fecha" name="evento_fecha" value="<?php echo esc_attr(get_post_meta($id, '_evento_fecha', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="evento_hora">Hora</label></th>
            <td><input type="time" id="evento_hora" name="evento_hora" value="<?php echo esc_attr(get_post_meta($id, '_evento_hora', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="evento_lugar">Lugar / Venue</label></th>
            <td><input type="text" id="evento_lugar" name="evento_lugar" value="<?php echo esc_attr(get_post_meta($id, '_evento_lugar', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="evento_ciudad">Ciudad</label></th>
            <td><input type="text" id="evento_ciudad" name="evento_ciudad" value="<?php echo esc_attr(get_post_meta($id, '_evento_ciudad', true)); ?>"></td>
        </tr>
        <tr>
            <th><label>País</label></th>
            <td>
                <?php
                $paises           = snow_get_paises();
                $pais_guardado    = get_post_meta($id, '_evento_pais', true);
                $bandera_guardada = get_post_meta($id, '_evento_pais_bandera', true);
                ?>
                <select name="evento_pais_select" id="evento-pais-select" style="width:100%">
                    <option value="">— Seleccionar país —</option>
                    <?php foreach ($paises as $codigo => $datos) : ?>
                        <option
                            value="<?php echo esc_attr($datos['nombre']); ?>"
                            data-bandera="<?php echo esc_attr($datos['bandera']); ?>"
                            <?php selected($pais_guardado, $datos['nombre']); ?>
                        >
                            <?php echo esc_html($datos['bandera'] . ' ' . $datos['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="evento-pais"    name="evento_pais"    value="<?php echo esc_attr($pais_guardado); ?>">
                <input type="hidden" id="evento-bandera" name="evento_bandera" value="<?php echo esc_attr($bandera_guardada); ?>">
                <script>
                (function() {
                    var sel = document.getElementById('evento-pais-select');
                    if (!sel) return;
                    sel.addEventListener('change', function() {
                        var opt = this.options[this.selectedIndex];
                        document.getElementById('evento-pais').value    = opt.value;
                        document.getElementById('evento-bandera').value = opt.getAttribute('data-bandera') || '';
                    });
                })();
                </script>
            </td>
        </tr>
        <tr>
            <th><label for="evento_talento_id">Talento</label></th>
            <td>
                <?php
                $talento_id_guardado = get_post_meta($id, '_evento_talento_id', true);
                $talentos = get_posts([
                    'post_type'      => 'talento',
                    'posts_per_page' => -1,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'post_status'    => 'publish',
                ]);
                ?>
                <select name="evento_talento_id" id="evento_talento_id" style="width:100%">
                    <option value="">— Seleccionar talento —</option>
                    <?php foreach ($talentos as $t) : ?>
                        <option value="<?php echo $t->ID; ?>" <?php selected($talento_id_guardado, $t->ID); ?>>
                            <?php echo esc_html($t->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="evento_link_compra">Link de compra</label></th>
            <td><input type="url" id="evento_link_compra" name="evento_link_compra" value="<?php echo esc_attr(get_post_meta($id, '_evento_link_compra', true)); ?>"></td>
        </tr>
        <tr>
            <th><label for="evento_descripcion">Descripción corta</label></th>
            <td><textarea id="evento_descripcion" name="evento_descripcion" rows="3"><?php echo esc_textarea(get_post_meta($id, '_evento_descripcion', true)); ?></textarea></td>
        </tr>
    </table>
    <?php
}

// === Metabox 2: Estado del Show ===
function snow_render_metabox_estados($post) {
    $estados = get_post_meta($post->ID, '_evento_estados', true);
    if (!is_array($estados)) $estados = [];
    wp_nonce_field('snow_guardar_estados', 'snow_estados_nonce');
    $opciones = [
        'nuevo'           => '🟢 Nuevo',
        'destacado'       => '⭐ Destacado',
        'ultimos-tickets' => '🔥 Últimos Tickets',
        'sold-out'        => '🔴 Sold Out',
        'una-semana'      => '⏰ A tan solo una semana',
        'nueva-fecha'     => '📅 Nueva Fecha Agregada',
    ];
    ?>
    <ul class="snow-estados-list">
    <?php foreach ($opciones as $value => $label) : ?>
        <li>
            <label>
                <input
                    type="checkbox"
                    name="evento_estados[]"
                    value="<?php echo esc_attr($value); ?>"
                    <?php checked(in_array($value, $estados, true)); ?>
                >
                <?php echo esc_html($label); ?>
            </label>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php
}

// === Metabox 3: Galería del Show ===
function snow_render_metabox_galeria($post) {
    $galeria_ids = get_post_meta($post->ID, '_evento_galeria', true);
    if (!is_array($galeria_ids)) $galeria_ids = [];
    wp_nonce_field('snow_guardar_galeria', 'snow_galeria_nonce');
    ?>
    <div id="snow-gallery-preview" class="snow-gallery-preview">
    <?php foreach ($galeria_ids as $gid) :
        $thumb = wp_get_attachment_image_url($gid, 'thumbnail');
        if (!$thumb) continue;
    ?>
        <div class="snow-gallery-item">
            <img src="<?php echo esc_url($thumb); ?>" alt="">
            <button type="button" class="snow-gallery-remove" title="Eliminar">&times;</button>
            <input type="hidden" name="evento_galeria[]" value="<?php echo esc_attr($gid); ?>">
        </div>
    <?php endforeach; ?>
    </div>
    <button type="button" id="snow-gallery-add" class="button button-secondary">
        Agregar imágenes
    </button>
    <script>
    jQuery(function($) {
        var frame;
        $('#snow-gallery-add').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Seleccionar imágenes para la galería',
                button: { text: 'Agregar a la galería' },
                multiple: true
            });
            frame.on('select', function() {
                frame.state().get('selection').each(function(attachment) {
                    var a = attachment.toJSON();
                    var thumb = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
                    $('#snow-gallery-preview').append(
                        '<div class="snow-gallery-item">' +
                        '<img src="' + thumb + '" alt="">' +
                        '<button type="button" class="snow-gallery-remove" title="Eliminar">&times;</button>' +
                        '<input type="hidden" name="evento_galeria[]" value="' + a.id + '">' +
                        '</div>'
                    );
                });
            });
            frame.open();
        });

        $(document).on('click', '.snow-gallery-remove', function() {
            $(this).closest('.snow-gallery-item').remove();
        });
    });
    </script>
    <?php
}

// === Save handler ===
function snow_guardar_metaboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'evento') return;

    // Detalles
    if (isset($_POST['snow_detalles_nonce']) && wp_verify_nonce($_POST['snow_detalles_nonce'], 'snow_guardar_detalles')) {
        $text_fields = ['evento_subtitulo', 'evento_fecha', 'evento_hora', 'evento_lugar', 'evento_ciudad'];
        foreach ($text_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        update_post_meta($post_id, '_evento_pais',         sanitize_text_field($_POST['evento_pais'] ?? ''));
        update_post_meta($post_id, '_evento_pais_bandera', sanitize_text_field($_POST['evento_bandera'] ?? ''));
        update_post_meta($post_id, '_evento_talento_id',   intval($_POST['evento_talento_id'] ?? 0));
        if (isset($_POST['evento_link_compra'])) {
            update_post_meta($post_id, '_evento_link_compra', esc_url_raw($_POST['evento_link_compra']));
        }
        if (isset($_POST['evento_descripcion'])) {
            update_post_meta($post_id, '_evento_descripcion', sanitize_textarea_field($_POST['evento_descripcion']));
        }
    }

    // Estados
    if (isset($_POST['snow_estados_nonce']) && wp_verify_nonce($_POST['snow_estados_nonce'], 'snow_guardar_estados')) {
        $estados = isset($_POST['evento_estados']) ? array_map('sanitize_text_field', (array) $_POST['evento_estados']) : [];
        update_post_meta($post_id, '_evento_estados', $estados);
    }

    // Galería
    if (isset($_POST['snow_galeria_nonce']) && wp_verify_nonce($_POST['snow_galeria_nonce'], 'snow_guardar_galeria')) {
        $galeria_ids = isset($_POST['evento_galeria']) ? array_filter(array_map('absint', (array) $_POST['evento_galeria'])) : [];
        update_post_meta($post_id, '_evento_galeria', array_values($galeria_ids));
    }
}
add_action('save_post', 'snow_guardar_metaboxes');
