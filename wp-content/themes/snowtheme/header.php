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

    <div class="snow-header__logo">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="snow-logo" aria-label="SNOW* — Inicio">
        SNOW<span class="snow-logo__star">*</span>
      </a>
    </div>

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
      <div class="snow-nav__social">
        <a href="https://www.instagram.com/snowentertainmentar/"
           target="_blank" rel="noopener noreferrer"
           class="snow-nav__social-link"
           aria-label="Instagram de SNOW*">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
            <circle cx="12" cy="12" r="4"/>
            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none"/>
          </svg>
          <span>Instagram</span>
        </a>
        <a href="https://www.youtube.com/watch?v=rFLy5-hOzoE"
           target="_blank" rel="noopener noreferrer"
           class="snow-nav__social-link"
           aria-label="YouTube de SNOW*">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
            <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="currentColor" stroke="none"/>
          </svg>
          <span>YouTube</span>
        </a>
      </div>
    </nav>

    <div class="snow-header__actions">
      <a href="<?php echo esc_url(home_url('/#shows')); ?>" class="btn btn--lime btn--pill snow-header__cta">
        PRÓXIMOS SHOWS
      </a>
      <button class="snow-hamburger" id="snow-hamburger"
              aria-label="Abrir menú" aria-expanded="false" aria-controls="snow-nav">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

  </div>

  <div class="snow-nav-overlay" id="snow-nav-overlay" aria-hidden="true"></div>
</header>
