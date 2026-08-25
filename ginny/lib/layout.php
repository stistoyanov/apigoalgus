<?php

declare(strict_types=1);

require_once __DIR__.'/helpers.php';

/**
 * @param array{
 *   title?:string,
 *   description?:string,
 *   active?:string,
 *   locale?:string,
 *   page?:string,
 *   paths?:array<string,string>
 * } $meta
 */
function ginny_header(array $meta = []): void
{
    $locale = ($meta['locale'] ?? 'bg') === 'en' ? 'en' : 'bg';
    $page = $meta['page'] ?? ($meta['active'] ?? 'home');
    if ($page === 'news') {
        $page = 'programa';
    }
    $paths = $meta['paths'] ?? ginny_paths($locale, $page);
    $base = $paths['base'];
    $active = $meta['active'] ?? 'home';

    $title = $meta['title'] ?? ginny_t('meta_home_title', $locale);
    $description = $meta['description'] ?? ginny_t('meta_home_desc', $locale);
    ?>
<!DOCTYPE html>
<html lang="<?= ginny_esc($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= ginny_esc($title) ?></title>
    <meta name="description" content="<?= ginny_esc($description) ?>">
    <link rel="icon" href="<?= ginny_esc(ginny_asset('images/favicon.png', $base)) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= ginny_esc(ginny_asset('images/favicon.png', $base)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ginny_esc(ginny_asset('css/site.css', $base)) ?>">
</head>
<body>
<a class="skip-link" href="#main"><?= ginny_esc(ginny_t('skip', $locale)) ?></a>
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="<?= ginny_esc($paths['home']) ?>">
            <img src="<?= ginny_esc(ginny_asset('images/logo.png', $base)) ?>" alt="Ginny Rock Bar" width="120" height="85">
            <span class="logo-text">GINNY</span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="<?= ginny_esc(ginny_t('menu', $locale)) ?>">
            <span></span><span></span><span></span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="<?= ginny_esc(ginny_t('nav_aria', $locale)) ?>">
            <ul>
                <li><a href="<?= ginny_esc($paths['home']) ?>" class="<?= $active === 'home' ? 'is-active' : '' ?>"><?= ginny_esc(ginny_t('nav_home', $locale)) ?></a></li>
                <li><a href="<?= ginny_esc($paths['news']) ?>" class="<?= $active === 'programa' ? 'is-active' : '' ?>"><?= ginny_esc(ginny_t('nav_news', $locale)) ?></a></li>
                <li><a href="<?= ginny_esc($paths['gallery']) ?>" class="<?= $active === 'gallery' ? 'is-active' : '' ?>"><?= ginny_esc(ginny_t('nav_gallery', $locale)) ?></a></li>
            </ul>
            <div class="lang-switch" aria-label="<?= ginny_esc(ginny_t('lang_aria', $locale)) ?>">
                <?php if ($locale === 'bg'): ?>
                    <span aria-current="page">BG</span><span class="lang-sep">/</span><a href="<?= ginny_esc($paths['lang_switch']) ?>">EN</a>
                <?php else: ?>
                    <a href="<?= ginny_esc($paths['lang_switch']) ?>">BG</a><span class="lang-sep">/</span><span aria-current="page">EN</span>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<?php
}

/**
 * @param array{locale?:string,base?:string,paths?:array<string,string>} $meta
 */
function ginny_footer(array $meta = []): void
{
    $locale = ($meta['locale'] ?? 'bg') === 'en' ? 'en' : 'bg';
    $paths = $meta['paths'] ?? null;
    $base = $meta['base'] ?? ($paths['base'] ?? '');
    ?>
<footer class="site-footer">
    <div class="footer-inner">
        <p class="footer-copy"><?= ginny_esc(ginny_t('footer_line', $locale)) ?></p>
        <ul class="social">
            <li><a href="https://www.facebook.com/Ginny.Bar.Sozopol" target="_blank" rel="noopener noreferrer">Facebook</a></li>
            <li><a href="https://www.instagram.com/ginnybar.sozopol/" target="_blank" rel="noopener noreferrer">Instagram</a></li>
        </ul>
    </div>
</footer>
<script src="<?= ginny_esc(ginny_asset('js/site.js', $base)) ?>" defer></script>
</body>
</html>
<?php
}
