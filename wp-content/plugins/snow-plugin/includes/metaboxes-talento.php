<?php
if (!defined('ABSPATH')) exit;

function snow_agregar_metaboxes_talento() {
    add_meta_box(
        'snow_talento_datos',
        'Datos del Talento',
        'snow_render_metabox_talento_datos',
        'talento',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'snow_agregar_metaboxes_talento');

function snow_render_metabox_talento_datos($post) {
    $id               = $post->ID;
    $paises           = snow_get_paises();
    $pais_guardado    = get_post_meta($id, '_talento_pais', true);
    $bandera_guardada = get_post_meta($id, '_talento_bandera', true);
    $orden            = get_post_meta($id, '_talento_orden', true);
    wp_nonce_field('snow_guardar_talento', 'snow_talento_nonce');
    ?>
    <p style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
        <label for="talento_orden" style="font-weight:600; display:block; margin-bottom:4px;">
            Orden de aparición
        </label>
        <input type="number"
               name="talento_orden"
               id="talento_orden"
               value="<?php echo esc_attr($orden !== '' ? $orden : ''); ?>"
               min="1"
               max="999"
               style="width: 80px; padding: 4px 8px;">
        <span style="color:#999; font-size:12px; margin-left:8px;">
            Número menor = aparece primero. Dejar vacío para ir al final.
        </span>
    </p>

    <p class="description" style="margin-bottom:12px;">
        La biografía se escribe en el editor principal de la página.
    </p>

    <p>
        <label><strong>País:</strong></label><br>
        <select name="talento_pais_select" id="talento-pais-select" style="width:100%">
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
        <input type="hidden" id="talento-pais"    name="talento_pais"    value="<?php echo esc_attr($pais_guardado); ?>">
        <input type="hidden" id="talento-bandera" name="talento_bandera" value="<?php echo esc_attr($bandera_guardada); ?>">
    </p>
    <script>
    (function() {
        var sel = document.getElementById('talento-pais-select');
        if (!sel) return;
        sel.addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            document.getElementById('talento-pais').value    = opt.value;
            document.getElementById('talento-bandera').value = opt.getAttribute('data-bandera') || '';
        });
    })();
    </script>

    <p>
        <label for="talento_instagram"><strong>Instagram:</strong></label><br>
        <input type="url" id="talento_instagram" name="talento_instagram" value="<?php echo esc_attr(get_post_meta($id, '_talento_instagram', true)); ?>" style="width:100%" placeholder="https://instagram.com/...">
    </p>
    <p>
        <label for="talento_youtube"><strong>YouTube:</strong></label><br>
        <input type="url" id="talento_youtube" name="talento_youtube" value="<?php echo esc_attr(get_post_meta($id, '_talento_youtube', true)); ?>" style="width:100%" placeholder="https://youtube.com/...">
    </p>
    <p>
        <label for="talento_spotify"><strong>Spotify:</strong></label><br>
        <input type="url" id="talento_spotify" name="talento_spotify" value="<?php echo esc_attr(get_post_meta($id, '_talento_spotify', true)); ?>" style="width:100%" placeholder="https://open.spotify.com/...">
    </p>
    <p>
        <label for="talento_tiktok"><strong>TikTok:</strong></label><br>
        <input type="url" id="talento_tiktok" name="talento_tiktok" value="<?php echo esc_attr(get_post_meta($id, '_talento_tiktok', true)); ?>" style="width:100%" placeholder="https://tiktok.com/@...">
    </p>
    <p>
        <label for="talento_web"><strong>Web oficial:</strong></label><br>
        <input type="url" id="talento_web" name="talento_web" value="<?php echo esc_attr(get_post_meta($id, '_talento_web', true)); ?>" style="width:100%" placeholder="https://...">
    </p>
    <?php
}

function snow_guardar_metaboxes_talento($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['snow_talento_nonce']) || !wp_verify_nonce($_POST['snow_talento_nonce'], 'snow_guardar_talento')) return;

    if (isset($_POST['talento_orden']) && $_POST['talento_orden'] !== '') {
        update_post_meta($post_id, '_talento_orden', absint($_POST['talento_orden']));
    } else {
        // Si se deja vacío, guardar 9999 para que vaya al final
        update_post_meta($post_id, '_talento_orden', 9999);
    }

    update_post_meta($post_id, '_talento_pais',    sanitize_text_field($_POST['talento_pais'] ?? ''));
    update_post_meta($post_id, '_talento_bandera', sanitize_text_field($_POST['talento_bandera'] ?? ''));

    $url_fields = ['talento_instagram', 'talento_youtube', 'talento_spotify', 'talento_tiktok', 'talento_web'];
    foreach ($url_fields as $field) {
        update_post_meta($post_id, '_' . $field, esc_url_raw($_POST[$field] ?? ''));
    }
}
add_action('save_post_talento', 'snow_guardar_metaboxes_talento');
