<?php
/**
 * Template Name: Elementor Canvas
 * Template Post Type: page
 *
 * Plantilla en blanco para landing pages con Elementor.
 * Sin header ni footer — Elementor toma control total.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('elementor-template-canvas'); ?>>
<?php wp_body_open(); ?>

<main>
    <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
