<?php
if (!defined('ABSPATH')) exit;

// ===================== Tabla DB =====================

function snow_crear_tabla_suscriptores() {
    global $wpdb;
    $tabla   = $wpdb->prefix . 'snow_suscriptores';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $tabla (
        id             BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        talento_id     BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
        talento_nombre VARCHAR(255)        NOT NULL DEFAULT '',
        nombre         VARCHAR(100)        NOT NULL DEFAULT '',
        apellido       VARCHAR(100)        NOT NULL DEFAULT '',
        email          VARCHAR(200)        NOT NULL DEFAULT '',
        fecha_registro DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY talento_id (talento_id),
        KEY email (email)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Crea la tabla en actualizaciones del plugin (sin reactivar)
add_action('plugins_loaded', function () {
    if (get_option('snow_plugin_version') !== '2.1') {
        snow_crear_tabla_suscriptores();
        update_option('snow_plugin_version', '2.1');
    }
});

// ===================== Admin: menú =====================

function snow_admin_menu_suscriptores() {
    add_submenu_page(
        'edit.php?post_type=evento',
        'Suscriptores',
        'Suscriptores',
        'manage_options',
        'snow-suscriptores',
        'snow_page_suscriptores'
    );
}
add_action('admin_menu', 'snow_admin_menu_suscriptores');

// ===================== Admin: página =====================

function snow_page_suscriptores() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $tabla = $wpdb->prefix . 'snow_suscriptores';

    $per_page   = 20;
    $paged      = max(1, absint($_GET['paged']      ?? 1));
    $offset     = ($paged - 1) * $per_page;
    $talento_id = absint($_GET['talento_id'] ?? 0);

    if ($talento_id) {
        $total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla WHERE talento_id = %d", $talento_id));
        $rows  = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabla WHERE talento_id = %d ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
            $talento_id, $per_page, $offset
        ));
    } else {
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM $tabla");
        $rows  = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabla ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
            $per_page, $offset
        ));
    }

    $total_pages  = (int) ceil($total / $per_page);
    $export_nonce = wp_create_nonce('snow_export_csv');
    $export_url   = admin_url('admin-post.php?action=snow_export_suscriptores&nonce=' . $export_nonce . ($talento_id ? '&talento_id=' . $talento_id : ''));

    $talentos = get_posts([
        'post_type'      => 'talento',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ]);
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Suscriptores a alertas</h1>
        <a href="<?php echo esc_url($export_url); ?>" class="page-title-action">Exportar CSV</a>

        <p style="margin-top:12px;color:#646970;">
            <?php printf('%d suscriptor%s en total', $total, $total !== 1 ? 'es' : ''); ?>
        </p>

        <form method="get" style="margin-bottom:16px;">
            <input type="hidden" name="post_type" value="evento">
            <input type="hidden" name="page"      value="snow-suscriptores">
            <select name="talento_id" onchange="this.form.submit()">
                <option value="0">Todos los talentos</option>
                <?php foreach ($talentos as $t) : ?>
                    <option value="<?php echo $t->ID; ?>" <?php selected($talento_id, $t->ID); ?>>
                        <?php echo esc_html($t->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit" class="button">Filtrar</button></noscript>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Talento</th>
                    <th>Fecha de registro</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows) : foreach ($rows as $row) : ?>
                <tr>
                    <td><?php echo esc_html($row->nombre); ?></td>
                    <td><?php echo esc_html($row->apellido); ?></td>
                    <td><?php echo esc_html($row->email); ?></td>
                    <td><?php echo esc_html($row->talento_nombre); ?></td>
                    <td><?php echo esc_html(date_i18n('d M Y H:i', strtotime($row->fecha_registro))); ?></td>
                </tr>
                <?php endforeach; else : ?>
                <tr>
                    <td colspan="5" style="text-align:center;color:#646970;padding:20px 0;">
                        No hay suscriptores todavía.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
        <div class="tablenav bottom" style="margin-top:12px;">
            <div class="tablenav-pages">
                <?php
                echo paginate_links([
                    'base'      => add_query_arg('paged', '%#%'),
                    'format'    => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total'     => $total_pages,
                    'current'   => $paged,
                ]);
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// ===================== Export CSV =====================

function snow_export_suscriptores_csv() {
    if (!current_user_can('manage_options')) wp_die('Sin permiso.');
    if (!wp_verify_nonce($_GET['nonce'] ?? '', 'snow_export_csv')) wp_die('Nonce inválido.');

    global $wpdb;
    $tabla      = $wpdb->prefix . 'snow_suscriptores';
    $talento_id = absint($_GET['talento_id'] ?? 0);

    $rows = $talento_id
        ? $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabla WHERE talento_id = %d ORDER BY fecha_registro DESC",
            $talento_id
          ))
        : $wpdb->get_results("SELECT * FROM $tabla ORDER BY fecha_registro DESC");

    $filename = 'suscriptores-snow-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF"); // BOM para Excel
    fputcsv($output, ['Nombre', 'Apellido', 'Email', 'Talento', 'Fecha de registro']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row->nombre,
            $row->apellido,
            $row->email,
            $row->talento_nombre,
            date_i18n('d/m/Y H:i', strtotime($row->fecha_registro)),
        ]);
    }
    fclose($output);
    exit;
}
add_action('admin_post_snow_export_suscriptores', 'snow_export_suscriptores_csv');

// ===================== AJAX suscripción =====================

function snow_ajax_suscribir() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'snow_suscribir_nonce')) {
        wp_send_json_error(['message' => 'Solicitud no válida.']);
    }

    $nombre         = sanitize_text_field($_POST['nombre']         ?? '');
    $apellido       = sanitize_text_field($_POST['apellido']       ?? '');
    $email          = sanitize_email($_POST['email']               ?? '');
    $talento_id     = absint($_POST['talento_id']                  ?? 0);
    $talento_nombre = sanitize_text_field($_POST['talento_nombre'] ?? '');

    if (!$nombre || !$apellido || !is_email($email)) {
        wp_send_json_error(['message' => 'Datos incompletos o inválidos.']);
    }

    global $wpdb;
    $tabla = $wpdb->prefix . 'snow_suscriptores';

    $existe = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $tabla WHERE email = %s AND talento_id = %d",
        $email, $talento_id
    ));
    if ($existe) {
        wp_send_json_success(['message' => 'Ya estás suscripto a este talento.']);
    }

    $inserted = $wpdb->insert($tabla, [
        'talento_id'     => $talento_id,
        'talento_nombre' => $talento_nombre,
        'nombre'         => $nombre,
        'apellido'       => $apellido,
        'email'          => $email,
        'fecha_registro' => current_time('mysql'),
    ]);

    if ($inserted) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['message' => 'Error al guardar. Intentá de nuevo.']);
    }
}
add_action('wp_ajax_snow_suscribir',        'snow_ajax_suscribir');
add_action('wp_ajax_nopriv_snow_suscribir', 'snow_ajax_suscribir');
