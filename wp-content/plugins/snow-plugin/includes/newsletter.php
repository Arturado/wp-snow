<?php
if (!defined('ABSPATH')) exit;

// ===================== Tabla DB =====================

function snow_crear_tabla_newsletter() {
    global $wpdb;
    $tabla   = $wpdb->prefix . 'snow_newsletter';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $tabla (
        id               BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email            VARCHAR(200) NOT NULL DEFAULT '',
        pais             VARCHAR(100) NOT NULL DEFAULT '',
        pais_bandera     VARCHAR(10)  NOT NULL DEFAULT '',
        tipo_suscripcion ENUM('solo_pais','todos') NOT NULL DEFAULT 'todos',
        fecha_registro   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

add_action('plugins_loaded', function () {
    if (get_option('snow_newsletter_version') !== '1.0') {
        snow_crear_tabla_newsletter();
        update_option('snow_newsletter_version', '1.0');
    }
});

// ===================== AJAX suscripción =====================

function snow_ajax_newsletter() {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', 'snow_newsletter_nonce')) {
        wp_send_json_error(['message' => 'Solicitud no válida.']);
    }

    $email = sanitize_email($_POST['email'] ?? '');
    $pais  = sanitize_text_field($_POST['pais'] ?? '');
    $tipo  = in_array($_POST['tipo'] ?? '', ['solo_pais', 'todos'], true) ? $_POST['tipo'] : 'todos';

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Ingresá un email válido.']);
    }
    if (!$pais) {
        wp_send_json_error(['message' => 'Seleccioná tu país de residencia.']);
    }

    $paises  = snow_get_paises();
    $bandera = '';
    foreach ($paises as $datos) {
        if ($datos['nombre'] === $pais) {
            $bandera = $datos['bandera'];
            break;
        }
    }

    global $wpdb;
    $tabla = $wpdb->prefix . 'snow_newsletter';

    $existe = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $tabla WHERE email = %s", $email
    ));

    if ($existe) {
        $wpdb->update($tabla,
            ['pais' => $pais, 'pais_bandera' => $bandera, 'tipo_suscripcion' => $tipo],
            ['email' => $email]
        );
        wp_send_json_success(['message' => '¡Preferencias actualizadas! Ya estabas suscripto.']);
    }

    $inserted = $wpdb->insert($tabla, [
        'email'            => $email,
        'pais'             => $pais,
        'pais_bandera'     => $bandera,
        'tipo_suscripcion' => $tipo,
        'fecha_registro'   => current_time('mysql'),
    ]);

    if ($inserted) {
        wp_send_json_success(['message' => '¡Listo! Te avisamos cuando haya nuevos shows. 🎉']);
    } else {
        wp_send_json_error(['message' => 'Error al guardar. Intentá de nuevo.']);
    }
}
add_action('wp_ajax_snow_newsletter',        'snow_ajax_newsletter');
add_action('wp_ajax_nopriv_snow_newsletter', 'snow_ajax_newsletter');

// ===================== Shortcode [snow_newsletter] =====================

function snow_shortcode_newsletter($atts) {
    $atts = shortcode_atts([
        'titulo'       => 'No te pierdas ningún show',
        'descripcion'  => 'Recibe en tu email los próximos shows que te interesan. Elige si quieres ver solo los eventos de tu país o de todos los países donde estamos.',
        'social_proof' => 'Más de 5.000 personas ya están suscritas.',
    ], $atts, 'snow_newsletter');

    $paises = snow_get_paises();
    $nonce  = wp_create_nonce('snow_newsletter_nonce');

    ob_start();
    ?>
    <div class="snow-newsletter" id="snow-newsletter-form">
        <div class="snow-newsletter__left">
            <h2 class="snow-newsletter__title">
                <?php echo esc_html(strtoupper($atts['titulo'])); ?>
            </h2>
            <p class="snow-newsletter__desc">
                <?php echo esc_html($atts['descripcion']); ?>
            </p>
            <?php if ($atts['social_proof']) : ?>
            <p class="snow-newsletter__proof">
                <strong><?php echo esc_html($atts['social_proof']); ?></strong>
            </p>
            <?php endif; ?>
        </div>

        <div class="snow-newsletter__right">
            <div class="snow-newsletter__card">

                <div id="snow-nl-msg" class="snow-nl-msg" hidden></div>

                <div class="snow-nl-field">
                    <label for="snow-nl-email">EMAIL</label>
                    <input type="email" id="snow-nl-email" placeholder="tu@email.com" autocomplete="email">
                </div>

                <div class="snow-nl-field">
                    <label for="snow-nl-pais">PAÍS DE RESIDENCIA</label>
                    <div class="snow-nl-select-wrap">
                        <select id="snow-nl-pais">
                            <option value="">— Seleccioná tu país —</option>
                            <?php foreach ($paises as $datos) : ?>
                            <option value="<?php echo esc_attr($datos['nombre']); ?>">
                                <?php echo esc_html($datos['bandera'] . ' ' . $datos['nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="snow-nl-field">
                    <label>¿QUÉ EVENTOS QUIERES RECIBIR?</label>
                    <div class="snow-nl-radios">
                        <label class="snow-nl-radio">
                            <input type="radio" name="snow_nl_tipo" value="solo_pais">
                            <span class="snow-nl-radio__custom"></span>
                            <span class="snow-nl-radio__label">Solo eventos en mi país</span>
                        </label>
                        <label class="snow-nl-radio">
                            <input type="radio" name="snow_nl_tipo" value="todos" checked>
                            <span class="snow-nl-radio__custom"></span>
                            <span class="snow-nl-radio__label">Todos los eventos <em>(recomendado)</em></span>
                        </label>
                    </div>
                </div>

                <button id="snow-nl-submit" class="snow-nl-btn">SUSCRIBIRME</button>

                <p class="snow-nl-legal">Te enviaremos un email para confirmar tu suscripción</p>

            </div>
        </div>
    </div>

    <script>
    (function() {
        const btn   = document.getElementById('snow-nl-submit');
        const msgEl = document.getElementById('snow-nl-msg');

        function showMsg(text, type) {
            msgEl.textContent = text;
            msgEl.className   = 'snow-nl-msg snow-nl-msg--' + type;
            msgEl.removeAttribute('hidden');
        }

        btn?.addEventListener('click', function() {
            const email = document.getElementById('snow-nl-email')?.value.trim();
            const pais  = document.getElementById('snow-nl-pais')?.value;
            const tipo  = document.querySelector('input[name="snow_nl_tipo"]:checked')?.value || 'todos';

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMsg('Ingresá un email válido.', 'error');
                return;
            }
            if (!pais) {
                showMsg('Seleccioná tu país de residencia.', 'error');
                return;
            }

            btn.disabled    = true;
            btn.textContent = 'Enviando...';

            const data = new FormData();
            data.append('action', 'snow_newsletter');
            data.append('nonce',  '<?php echo esc_js($nonce); ?>');
            data.append('email',  email);
            data.append('pais',   pais);
            data.append('tipo',   tipo);

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', { method: 'POST', body: data })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        showMsg(res.data?.message || '¡Suscripto!', 'success');
                        btn.textContent = '¡Suscripto! 🎉';
                    } else {
                        showMsg(res.data?.message || 'Error. Intentá de nuevo.', 'error');
                        btn.disabled    = false;
                        btn.textContent = 'SUSCRIBIRME';
                    }
                })
                .catch(() => {
                    showMsg('Error de conexión.', 'error');
                    btn.disabled    = false;
                    btn.textContent = 'SUSCRIBIRME';
                });
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('snow_newsletter', 'snow_shortcode_newsletter');

// ===================== Encolar CSS del shortcode =====================

function snow_newsletter_assets() {
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'snow_newsletter')) {
        wp_enqueue_style(
            'snow-newsletter',
            SNOW_PLUGIN_URL . 'assets/css/newsletter.css',
            [],
            '1.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'snow_newsletter_assets');

// ===================== Admin: menú =====================

function snow_admin_menu_newsletter() {
    add_submenu_page(
        'edit.php?post_type=evento',
        'Newsletter',
        'Newsletter',
        'manage_options',
        'snow-newsletter',
        'snow_page_newsletter'
    );
}
add_action('admin_menu', 'snow_admin_menu_newsletter');

// ===================== Admin: página =====================

function snow_page_newsletter() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $tabla = $wpdb->prefix . 'snow_newsletter';

    $per_page = 20;
    $paged    = max(1, absint($_GET['paged'] ?? 1));
    $offset   = ($paged - 1) * $per_page;
    $tipo     = sanitize_text_field($_GET['tipo'] ?? '');
    $tipo     = in_array($tipo, ['solo_pais', 'todos'], true) ? $tipo : '';

    if ($tipo) {
        $total = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $tabla WHERE tipo_suscripcion = %s", $tipo));
        $rows  = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabla WHERE tipo_suscripcion = %s ORDER BY fecha_registro DESC LIMIT %d OFFSET %d",
            $tipo, $per_page, $offset
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
    $export_url   = admin_url('admin-post.php?action=snow_export_newsletter&nonce=' . $export_nonce . ($tipo ? '&tipo=' . $tipo : ''));

    $etiquetas_tipo = [
        'solo_pais' => 'Solo su país',
        'todos'     => 'Todos los eventos',
    ];
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Suscriptores Newsletter</h1>
        <a href="<?php echo esc_url($export_url); ?>" class="page-title-action">Exportar CSV</a>

        <p style="margin-top:12px;color:#646970;">
            <?php printf('%d suscriptor%s en total', $total, $total !== 1 ? 'es' : ''); ?>
        </p>

        <form method="get" style="margin-bottom:16px;">
            <input type="hidden" name="post_type" value="evento">
            <input type="hidden" name="page"      value="snow-newsletter">
            <select name="tipo" onchange="this.form.submit()">
                <option value="">Todas las preferencias</option>
                <option value="solo_pais" <?php selected($tipo, 'solo_pais'); ?>>Solo su país</option>
                <option value="todos" <?php selected($tipo, 'todos'); ?>>Todos los eventos</option>
            </select>
            <noscript><button type="submit" class="button">Filtrar</button></noscript>
        </form>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>País</th>
                    <th>Preferencia</th>
                    <th>Fecha de registro</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows) : foreach ($rows as $row) : ?>
                <tr>
                    <td><?php echo esc_html($row->email); ?></td>
                    <td><?php echo esc_html(trim($row->pais_bandera . ' ' . $row->pais)); ?></td>
                    <td><?php echo esc_html($etiquetas_tipo[$row->tipo_suscripcion] ?? $row->tipo_suscripcion); ?></td>
                    <td><?php echo esc_html(date_i18n('d M Y H:i', strtotime($row->fecha_registro))); ?></td>
                </tr>
                <?php endforeach; else : ?>
                <tr>
                    <td colspan="4" style="text-align:center;color:#646970;padding:20px 0;">
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

function snow_export_newsletter_csv() {
    if (!current_user_can('manage_options')) wp_die('Sin permiso.');
    if (!wp_verify_nonce($_GET['nonce'] ?? '', 'snow_export_csv')) wp_die('Nonce inválido.');

    global $wpdb;
    $tabla = $wpdb->prefix . 'snow_newsletter';
    $tipo  = sanitize_text_field($_GET['tipo'] ?? '');
    $tipo  = in_array($tipo, ['solo_pais', 'todos'], true) ? $tipo : '';

    $rows = $tipo
        ? $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $tabla WHERE tipo_suscripcion = %s ORDER BY fecha_registro DESC",
            $tipo
          ))
        : $wpdb->get_results("SELECT * FROM $tabla ORDER BY fecha_registro DESC");

    $etiquetas_tipo = [
        'solo_pais' => 'Solo su país',
        'todos'     => 'Todos los eventos',
    ];

    $filename = 'newsletter-snow-' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF"); // BOM para Excel
    fputcsv($output, ['Email', 'País', 'Preferencia', 'Fecha de registro']);
    foreach ($rows as $row) {
        fputcsv($output, [
            $row->email,
            trim($row->pais_bandera . ' ' . $row->pais),
            $etiquetas_tipo[$row->tipo_suscripcion] ?? $row->tipo_suscripcion,
            date_i18n('d/m/Y H:i', strtotime($row->fecha_registro)),
        ]);
    }
    fclose($output);
    exit;
}
add_action('admin_post_snow_export_newsletter', 'snow_export_newsletter_csv');
