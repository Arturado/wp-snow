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
      <a href="<?php echo esc_url(home_url('/')); ?>"
         class="snow-logo"
         aria-label="SNOW* Entertainment — Inicio">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1222.63 367.18"
             class="snow-logo__svg" aria-hidden="true" focusable="false">
            <defs>
                <style>
                    .snow-logo-white { fill: #ffffff; }
                    .snow-logo-blue  { fill: #0abffd; }
                </style>
            </defs>
            <g>
                <g>
                    <path class="snow-logo-blue" d="M1222.63,87.82c-5.2-22.24-16.93-41.98-33.05-57.07l-54.61,41.84,9.68-67.76c-10.42-3.14-21.47-4.83-32.9-4.83s-22.32,1.66-32.67,4.76l9.45,68.69-54.93-42.4c-15.96,15.06-27.57,34.69-32.74,56.79l64.72,26.6-64.56,26.13c5.23,21.75,16.7,41.08,32.42,55.97l56.23-43.39-10.25,69.94c10.25,3.03,21.09,4.66,32.32,4.66s22.07-1.62,32.32-4.66l-10.25-70.23,56.25,43.67c15.78-14.96,27.28-34.38,32.47-56.25l-65.22-25.83,65.3-26.63Z"/>
                    <g>
                        <path class="snow-logo-white" d="M75.85,160.65c1.85,12.63,17.25,20.95,34.81,20.95,19.56,0,31.57-7.85,31.57-18.64s-8.16-14.78-32.04-19.1l-29.42-5.24c-40.82-7.08-62.99-30.49-62.99-65.3C17.79,28.34,56.29,0,109.28,0c59.15,0,92.26,27.11,92.57,70.23h-59.61c-.46-13.86-14.17-22.03-31.73-22.03s-27.88,7.08-27.88,18.18,9.09,16.17,30.19,19.87l30.5,5.24c43.13,7.55,63.3,28.03,63.3,64.07,0,45.44-38.04,74.4-97.18,74.4S14.24,205.01,14.09,160.65h61.76Z"/>
                        <path class="snow-logo-white" d="M231.76,226.11V3.85h50.67l85.79,115.2h1.23V3.85h61.61v222.25h-50.06l-86.41-117.06h-1.23v117.06h-61.61Z"/>
                        <path class="snow-logo-white" d="M682.09,115.05c0,71.01-43.28,114.91-111.97,114.91s-111.97-43.9-111.97-114.91S501.43,0,570.12,0s111.97,44.05,111.97,115.05ZM524.68,115.05c0,38.36,18.02,62.85,45.43,62.85s45.44-24.49,45.44-62.85-18.18-62.99-45.44-62.99-45.43,24.49-45.43,62.99Z"/>
                        <path class="snow-logo-white" d="M805.84,226.11h-61.61L686.93,3.85h67.46l25.57,140.47h1.23L814.31,3.85h50.52l33.12,140.47h1.23L924.74,3.85h67.46l-57.3,222.25h-61.61l-33.11-127.22h-1.23l-33.11,127.22Z"/>
                    </g>
                </g>
                <g>
                    <path class="snow-logo-white" d="M72.64,367.18H0v-102.96h72.64v24.05H30.25v16.63h39.75v21.69H30.25v16.55h42.39v24.05Z"/>
                    <path class="snow-logo-white" d="M86.76,367.18v-102.96h23.48l39.75,53.37h.57v-53.37h28.54v102.96h-23.19l-40.03-54.23h-.57v54.23h-28.54Z"/>
                    <path class="snow-logo-white" d="M216.55,367.18v-78.92h-27.11v-24.05h84.48v24.05h-27.11v78.92h-30.26Z"/>
                    <path class="snow-logo-white" d="M356.9,367.18h-72.64v-102.96h72.64v24.05h-42.39v16.63h39.75v21.69h-39.75v16.55h42.39v24.05Z"/>
                    <path class="snow-logo-white" d="M401.27,367.18h-30.25v-102.96h45.74c27.47,0,41.6,12.91,41.6,34.96,0,12.13-5.85,23.83-16.62,28.97l19.55,39.03h-33.82l-16.27-34.54h-9.92v34.54ZM401.27,312.66h12.49c8.49,0,13.7-5.21,13.7-12.99s-5.49-13.13-13.63-13.13h-12.56v26.12Z"/>
                    <path class="snow-logo-white" d="M493.74,367.18v-78.92h-27.11v-24.05h84.48v24.05h-27.11v78.92h-30.26Z"/>
                    <path class="snow-logo-white" d="M610.47,346.7h-31.4l-5.35,20.48h-30.83l33.61-102.96h37.96l33.61,102.96h-32.25l-5.35-20.48ZM584.57,325.37h20.41l-9.92-37.39h-.57l-9.92,37.39Z"/>
                    <path class="snow-logo-white" d="M657.34,367.18v-102.96h30.25v102.96h-30.25Z"/>
                    <path class="snow-logo-white" d="M701.86,367.18v-102.96h23.48l39.75,53.37h.57v-53.37h28.54v102.96h-23.19l-40.03-54.23h-.57v54.23h-28.54Z"/>
                    <path class="snow-logo-white" d="M898.58,367.18v-59.65h-.57l-23.19,58.44h-16.2l-23.19-58.44h-.57v59.65h-26.4v-102.96h34.96l23.05,60.08h.57l22.98-60.08h34.96v102.96h-26.4Z"/>
                    <path class="snow-logo-white" d="M1011.88,367.18h-72.64v-102.96h72.64v24.05h-42.39v16.63h39.75v21.69h-39.75v16.55h42.39v24.05Z"/>
                    <path class="snow-logo-white" d="M1026,367.18v-102.96h23.48l39.75,53.37h.57v-53.37h28.54v102.96h-23.19l-40.03-54.23h-.57v54.23h-28.54Z"/>
                    <path class="snow-logo-white" d="M1155.79,367.18v-78.92h-27.11v-24.05h84.48v24.05h-27.11v78.92h-30.26Z"/>
                </g>
            </g>
        </svg>
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
