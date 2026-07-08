<?php get_header(); ?>

<main class="snow-page-main audiovisual-page">

    <!-- Mini hero de la página -->
    <section class="audiovisual-hero">
        <div class="container">
            <h1 class="audiovisual-hero__title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:inline-block;vertical-align:middle;margin-right:0.5rem;color:var(--snow-lime)">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                </svg>
                ZONA AUDIOVISUAL
            </h1>
            <p class="audiovisual-hero__subtitle">Todos nuestros shows en video</p>
        </div>
    </section>

    <!-- Filtro por categoría (tabs simples) -->
    <section class="audiovisual-filter">
        <div class="container">
            <div class="audiovisual-tabs" id="audiovisual-tabs">
                <button class="audiovisual-tab is-active" data-cat="">Todos</button>
                <?php
                $cats = get_terms(['taxonomy' => 'snow_video_cat', 'hide_empty' => true]);
                if (!is_wp_error($cats)) foreach ($cats as $cat) :
                ?>
                <button class="audiovisual-tab" data-cat="<?php echo esc_attr($cat->slug); ?>">
                    <?php echo esc_html(strtoupper($cat->name)); ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Grid de todos los videos -->
    <section class="audiovisual-grid-section">
        <div class="container">
            <div class="video-grid video-grid--full" id="audiovisual-grid">
                <?php
                $all_videos = new WP_Query([
                    'post_type'      => 'video',
                    'posts_per_page' => -1,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ]);

                while ($all_videos->have_posts()) : $all_videos->the_post();
                    $vid_id    = get_the_ID();
                    $vid_titulo = get_the_title();
                    $vid_url   = get_post_meta($vid_id, '_video_youtube_url', true);
                    $vid_yt_id = snow_get_youtube_id($vid_url);
                    $vid_thumb = get_the_post_thumbnail_url($vid_id, 'large')
                                 ?: ($vid_yt_id ? "https://img.youtube.com/vi/{$vid_yt_id}/maxresdefault.jpg" : '');
                    $vid_cat_terms = get_the_terms($vid_id, 'snow_video_cat');
                    $vid_cat   = ($vid_cat_terms && !is_wp_error($vid_cat_terms)) ? $vid_cat_terms[0]->name : '';
                    $vid_cat_slug = ($vid_cat_terms && !is_wp_error($vid_cat_terms)) ? $vid_cat_terms[0]->slug : '';
                ?>
                <div class="video-card"
                     data-youtube-id="<?php echo esc_attr($vid_yt_id); ?>"
                     data-titulo="<?php echo esc_attr($vid_titulo); ?>"
                     data-cat="<?php echo esc_attr($vid_cat_slug); ?>"
                     role="button" tabindex="0"
                     aria-label="Ver video: <?php echo esc_attr($vid_titulo); ?>">
                    <div class="video-card__thumb"
                         style="background-image: url('<?php echo esc_url($vid_thumb); ?>')">
                        <?php if ($vid_cat) : ?>
                        <span class="video-card__cat"><?php echo esc_html(strtoupper($vid_cat)); ?></span>
                        <?php endif; ?>
                        <button class="video-card__play" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="video-card__info">
                        <h3 class="video-card__title"><?php echo esc_html($vid_titulo); ?></h3>
                    </div>
                </div>
                <?php endwhile;
                if (!$all_videos->have_posts()) : ?>
                <p class="audiovisual-empty">Próximamente subiremos videos de nuestros shows.</p>
                <?php endif;
                wp_reset_postdata(); ?>
            </div>
        </div>
    </section>

</main>

<!-- Mismo modal que el home -->
<div id="snow-video-modal" class="snow-video-modal" hidden aria-modal="true" role="dialog" aria-label="Reproductor de video">
    <div class="snow-video-modal__overlay" id="snow-video-modal-overlay"></div>
    <div class="snow-video-modal__box">
        <button class="snow-video-modal__close" id="snow-video-modal-close" aria-label="Cerrar video">✕</button>
        <div class="snow-video-modal__iframe-wrap">
            <iframe id="snow-video-iframe" src="" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen title="Video"></iframe>
        </div>
    </div>
</div>

<?php get_footer(); ?>
