<?php get_header(); ?>

<main id="snow-page-content" class="snow-page-main">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('snow-page-article'); ?>>
            <?php
            if (function_exists('elementor_theme_do_location') && elementor_theme_do_location('single')) {
                // Elementor maneja el contenido
            } else {
                ?>
                <div class="snow-page-content container">
                    <?php the_content(); ?>
                </div>
                <?php
            }
            ?>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
