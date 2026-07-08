<?php
if (!defined('ABSPATH')) exit;

function snow_get_youtube_id($url) {
    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
    return $matches[1] ?? '';
}

function snow_agregar_metaboxes_video() {
    add_meta_box(
        'snow_video_datos',
        'Datos del Video',
        'snow_render_metabox_video_datos',
        'video',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'snow_agregar_metaboxes_video');

function snow_render_metabox_video_datos($post) {
    $id  = $post->ID;
    $url = get_post_meta($id, '_video_youtube_url', true);
    wp_nonce_field('snow_guardar_video', 'snow_video_nonce');
    ?>
    <p>
        <label for="video_youtube_url"><strong>URL de YouTube:</strong></label><br>
        <input type="url" id="video_youtube_url" name="video_youtube_url" value="<?php echo esc_attr($url); ?>" style="width:100%" placeholder="https://www.youtube.com/watch?v=XXXX">
    </p>
    <?php
}

function snow_guardar_metaboxes_video($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (!isset($_POST['snow_video_nonce']) || !wp_verify_nonce($_POST['snow_video_nonce'], 'snow_guardar_video')) return;

    update_post_meta($post_id, '_video_youtube_url', esc_url_raw($_POST['video_youtube_url'] ?? ''));
}
add_action('save_post_video', 'snow_guardar_metaboxes_video');
