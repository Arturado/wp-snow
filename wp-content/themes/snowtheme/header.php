<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <?php wp_head(); ?>
</head>
<body <?php body_class('snow-body'); ?>>
<header class="snow-header" id="snow-header">
  <div class="container snow-header__inner">

    <a href="<?php echo esc_url(home_url('/')); ?>" class="snow-logo" aria-label="SNOW* — Inicio">
      SNOW<span class="snow-logo__star">*</span>
    </a>

    <button class="nav-toggle" id="nav-toggle" aria-expanded="false" aria-label="Abrir menú">
      <span class="nav-toggle__bar"></span>
      <span class="nav-toggle__bar"></span>
      <span class="nav-toggle__bar"></span>
    </button>

    <nav class="snow-nav" id="snow-nav" aria-label="Menú principal">
      <?php
      wp_nav_menu([
          'theme_location' => 'main-menu',
          'menu_class'     => 'snow-nav__list',
          'container'      => false,
          'fallback_cb'    => false,
          'depth'          => 1,
      ]);
      ?>
    </nav>

    <a href="<?php echo esc_url(home_url('/#shows')); ?>" class="btn btn--lime btn--pill snow-header__cta">
      PRÓXIMOS SHOWS
    </a>

  </div>
</header>
