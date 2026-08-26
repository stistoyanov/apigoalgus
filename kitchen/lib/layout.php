<?php

declare(strict_types=1);

require_once __DIR__.'/helpers.php';
require_once __DIR__.'/api-client.php';

/**
 * @param array{
 *   title?:string,
 *   description?:string,
 *   active?:string,
 *   locale?:string,
 *   page?:string,
 *   paths?:array<string,string>,
 *   payload?:array<string, mixed>
 * } $meta
 */
function kitchen_header(array $meta = []): void
{
    $locale = ($meta['locale'] ?? 'bg') === 'en' ? 'en' : 'bg';
    $page = $meta['page'] ?? ($meta['active'] ?? 'home');
    if ($page === 'menu') {
        $page = 'ginnys-menu';
    }
    $paths = $meta['paths'] ?? kitchen_paths($locale, $page);
    $base = $paths['base'];
    $active = $meta['active'] ?? 'home';
    $payload = $meta['payload'] ?? null;

    $title = $meta['title'] ?? kitchen_t('meta_home_title', $locale);
    $description = $meta['description'] ?? kitchen_t('meta_home_desc', $locale);
    $navHome = is_array($payload) ? kitchen_c($payload, 'nav', 'home', kitchen_t('nav_home', $locale)) : kitchen_t('nav_home', $locale);
    $navMenu = is_array($payload) ? kitchen_c($payload, 'nav', 'menu', kitchen_t('nav_menu', $locale)) : kitchen_t('nav_menu', $locale);
    $navBar = is_array($payload) ? kitchen_c($payload, 'nav', 'bar', kitchen_t('nav_bar', $locale)) : kitchen_t('nav_bar', $locale);
    $navReserve = is_array($payload) ? kitchen_c($payload, 'nav', 'reserve', kitchen_t('nav_reserve', $locale)) : kitchen_t('nav_reserve', $locale);
    $barUrl = is_array($payload)
        ? ($locale === 'en'
            ? kitchen_s($payload, 'sister_url_en', kitchen_bar_url('en'))
            : kitchen_s($payload, 'sister_url', kitchen_bar_url('bg')))
        : kitchen_bar_url($locale);
    $phoneE164 = is_array($payload)
        ? kitchen_s($payload, 'phone_e164', '+359886060687')
        : '+359886060687';
    $favicon = is_array($payload)
        ? kitchen_media_url($payload, 'favicon', 'images/favicon.png', $base)
        : kitchen_asset('images/favicon.png', $base);
    $logo = is_array($payload)
        ? kitchen_media_url($payload, 'logo', 'images/logo.png', $base)
        : kitchen_asset('images/logo.png', $base);
    ?>
<!DOCTYPE html>
<html lang="<?= kitchen_esc($locale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= kitchen_esc($title) ?></title>
    <meta name="description" content="<?= kitchen_esc($description) ?>">
    <link rel="icon" href="<?= kitchen_esc($favicon) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= kitchen_esc($favicon) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= kitchen_esc(kitchen_asset('css/site.css', $base)) ?>">
</head>
<body>
<a class="skip-link" href="#main"><?= kitchen_esc(kitchen_t('skip', $locale)) ?></a>
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="<?= kitchen_esc($paths['home']) ?>">
            <img src="<?= kitchen_esc($logo) ?>" alt="Ginny's Kitchen" width="150" height="60">
            <span class="logo-text">KITCHEN</span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="<?= kitchen_esc(kitchen_t('menu', $locale)) ?>">
            <span></span><span></span><span></span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="<?= kitchen_esc(kitchen_t('nav_aria', $locale)) ?>">
            <ul>
                <li><a href="<?= kitchen_esc($paths['home']) ?>" class="<?= $active === 'home' ? 'is-active' : '' ?>"><?= kitchen_esc($navHome) ?></a></li>
                <li><a href="<?= kitchen_esc($paths['menu']) ?>" class="<?= $active === 'menu' ? 'is-active' : '' ?>"><?= kitchen_esc($navMenu) ?></a></li>
                <li><a href="<?= kitchen_esc($barUrl) ?>" target="_blank" rel="noopener noreferrer"><?= kitchen_esc($navBar) ?></a></li>
                <li><a class="cta-nav" href="tel:<?= kitchen_esc($phoneE164) ?>"><?= kitchen_esc($navReserve) ?></a></li>
            </ul>
            <div class="lang-switch" aria-label="<?= kitchen_esc(kitchen_t('lang_aria', $locale)) ?>">
                <?php if ($locale === 'bg'): ?>
                    <span aria-current="page">BG</span><span class="lang-sep">/</span><a href="<?= kitchen_esc($paths['lang_switch']) ?>">EN</a>
                <?php else: ?>
                    <a href="<?= kitchen_esc($paths['lang_switch']) ?>">BG</a><span class="lang-sep">/</span><span aria-current="page">EN</span>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<?php
}

/**
 * @param array{
 *   locale?:string,
 *   base?:string,
 *   paths?:array<string,string>,
 *   payload?:array<string, mixed>
 * } $meta
 */
function kitchen_footer(array $meta = []): void
{
    $locale = ($meta['locale'] ?? 'bg') === 'en' ? 'en' : 'bg';
    $paths = $meta['paths'] ?? null;
    $base = $meta['base'] ?? ($paths['base'] ?? '');
    $payload = $meta['payload'] ?? null;

    $footerLoc = is_array($payload) ? kitchen_c($payload, 'footer', 'loc', kitchen_t('footer_loc', $locale)) : kitchen_t('footer_loc', $locale);
    $footerHoursLabel = is_array($payload) ? kitchen_c($payload, 'footer', 'hours_label', kitchen_t('footer_hours_label', $locale)) : kitchen_t('footer_hours_label', $locale);
    $footerHours = is_array($payload) ? kitchen_c($payload, 'footer', 'hours', kitchen_t('footer_hours', $locale)) : kitchen_t('footer_hours', $locale);
    $footerBook = is_array($payload) ? kitchen_c($payload, 'footer', 'book', kitchen_t('footer_book', $locale)) : kitchen_t('footer_book', $locale);
    $phone = is_array($payload) ? kitchen_s($payload, 'phone', '+359 88 606 0687') : '+359 88 606 0687';
    $phoneE164 = is_array($payload) ? kitchen_s($payload, 'phone_e164', '+359886060687') : '+359886060687';
    $email = is_array($payload) ? kitchen_s($payload, 'email', 'ginnys.kitchen@abv.bg') : 'ginnys.kitchen@abv.bg';
    $facebook = is_array($payload) ? kitchen_s($payload, 'facebook_url', 'https://www.facebook.com/Ginny.Bar.Sozopol') : 'https://www.facebook.com/Ginny.Bar.Sozopol';
    $instagram = is_array($payload) ? kitchen_s($payload, 'instagram_url', 'https://www.instagram.com/ginnybar.sozopol/') : 'https://www.instagram.com/ginnybar.sozopol/';
    ?>
<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <p class="footer-brand">Ginny's Kitchen</p>
            <p><?= kitchen_esc($footerLoc) ?></p>
            <p>
                <strong><?= kitchen_esc($footerHoursLabel) ?>:</strong>
                <?= kitchen_esc($footerHours) ?>
            </p>
            <p>
                <strong><?= kitchen_esc($footerBook) ?>:</strong>
                <a href="tel:<?= kitchen_esc($phoneE164) ?>"><?= kitchen_esc($phone) ?></a>
            </p>
            <p><strong>Email:</strong> <a href="mailto:<?= kitchen_esc($email) ?>"><?= kitchen_esc($email) ?></a></p>
        </div>
        <ul class="social">
            <li><a href="<?= kitchen_esc($facebook) ?>" target="_blank" rel="noopener noreferrer">Facebook</a></li>
            <li><a href="<?= kitchen_esc($instagram) ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li>
        </ul>
    </div>
</footer>
<script src="<?= kitchen_esc(kitchen_asset('js/site.js', $base)) ?>" defer></script>
</body>
</html>
<?php
}
