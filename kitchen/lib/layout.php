<?php

declare(strict_types=1);

require_once __DIR__.'/helpers.php';

/**
 * @param array{
 *   title?:string,
 *   description?:string,
 *   active?:string,
 *   base?:string,
 *   locale?:string,
 *   home_url?:string,
 *   menu_url?:string,
 *   lang_url?:string
 * } $meta
 */
function kitchen_header(array $meta = []): void
{
    $locale = $meta['locale'] ?? 'bg';
    $isEn = $locale === 'en';
    $title = $meta['title'] ?? ($isEn
        ? "Ginny's Kitchen – Your favorite restaurant in Sozopol!"
        : "Ginny's Kitchen – Любимият ресторант в Созопол!");
    $description = $meta['description'] ?? ($isEn
        ? 'Seafood, Bulgarian classics and international dishes by the sea in Sozopol.'
        : 'Морски дарове, българска кухня и международни специалитети до морето в Созопол.');
    $active = $meta['active'] ?? 'home';
    $base = $meta['base'] ?? '';

    $homeUrl = $meta['home_url'] ?? ($isEn ? 'en/' : './');
    $menuUrl = $meta['menu_url'] ?? 'ginnys-menu/';
    $langUrl = $meta['lang_url'] ?? ($isEn ? '../' : 'en/');

    $navHome = $isEn ? 'Home' : 'Начало';
    $navMenu = $isEn ? 'Menu' : 'Меню';
    $navReserve = $isEn ? 'Reservation' : 'Резервация';
    $menuLabel = $isEn ? 'Menu' : 'Меню';
    $skip = $isEn ? 'Skip to content' : 'Към съдържанието';
    ?>
<!DOCTYPE html>
<html lang="<?= $isEn ? 'en' : 'bg' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= kitchen_esc($title) ?></title>
    <meta name="description" content="<?= kitchen_esc($description) ?>">
    <link rel="icon" href="<?= kitchen_esc(kitchen_asset('images/favicon.png', $base)) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= kitchen_esc(kitchen_asset('images/favicon.png', $base)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abyssinica+SIL&family=Clicker+Script&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= kitchen_esc(kitchen_asset('css/site.css', $base)) ?>">
</head>
<body>
<a class="skip-link" href="#main"><?= kitchen_esc($skip) ?></a>
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="<?= kitchen_esc($homeUrl) ?>">
            <img src="<?= kitchen_esc(kitchen_asset('images/logo.png', $base)) ?>" alt="Ginny's Kitchen Sozopol" width="150" height="60">
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="<?= kitchen_esc($menuLabel) ?>">
            <span></span><span></span><span></span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="<?= $isEn ? 'Main navigation' : 'Основна навигация' ?>">
            <ul>
                <li><a href="<?= kitchen_esc($homeUrl) ?>" class="<?= $active === 'home' ? 'is-active' : '' ?>"><?= kitchen_esc($navHome) ?></a></li>
                <li><a href="<?= kitchen_esc($menuUrl) ?>" class="<?= $active === 'menu' ? 'is-active' : '' ?>"><?= kitchen_esc($navMenu) ?></a></li>
                <li><a class="lang" href="<?= kitchen_esc($langUrl) ?>"><?= $isEn ? 'BG' : 'EN' ?></a></li>
                <li><a class="cta-nav" href="tel:+359886060687"><?= kitchen_esc($navReserve) ?></a></li>
            </ul>
        </nav>
    </div>
</header>
<?php
}

/**
 * @param array{base?:string,locale?:string} $meta
 */
function kitchen_footer(array $meta = []): void
{
    $base = $meta['base'] ?? '';
    $isEn = ($meta['locale'] ?? 'bg') === 'en';
    ?>
<footer class="site-footer">
    <div class="footer-inner">
        <div>
            <p class="footer-brand">Ginny's Kitchen</p>
            <p><?= $isEn ? 'Sozopol, Central Beach' : 'Созопол, Централен Плаж' ?></p>
            <p>
                <strong><?= $isEn ? 'Hours:' : 'Работно време:' ?></strong>
                <?= $isEn ? 'Monday – Sunday: 9:00–23:00' : 'Понеделник – Неделя: 9-23ч.' ?>
            </p>
            <p>
                <strong><?= $isEn ? 'Reservations:' : 'За резервации:' ?></strong>
                <a href="tel:+359886060687">+359 886 060 687</a>
            </p>
            <p><strong>Email:</strong> <a href="mailto:ginnys.kitchen@abv.bg">ginnys.kitchen@abv.bg</a></p>
        </div>
        <ul class="social">
            <li><a href="https://www.facebook.com/Ginny.Bar.Sozopol" target="_blank" rel="noopener noreferrer">Facebook</a></li>
            <li><a href="https://www.instagram.com/ginnybar.sozopol/" target="_blank" rel="noopener noreferrer">Instagram</a></li>
        </ul>
    </div>
</footer>
<script src="<?= kitchen_esc(kitchen_asset('js/site.js', $base)) ?>" defer></script>
</body>
</html>
<?php
}
