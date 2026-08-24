<?php

declare(strict_types=1);

require_once __DIR__.'/helpers.php';

/**
 * @param array{title?:string,description?:string,active?:string,base?:string} $meta
 */
function ginny_header(array $meta = []): void
{
    $title = $meta['title'] ?? 'Ginny Rock Bar Sozopol | Най-добрият бар в Созопол';
    $description = $meta['description'] ?? 'Рок бар в сърцето на Созопол – Централен плаж. Жива музика, студена бира, коктейли. 24/7.';
    $active = $meta['active'] ?? 'home';
    $base = $meta['base'] ?? '';
    $home = $base === '' ? './' : $base;
    ?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= ginny_esc($title) ?></title>
    <meta name="description" content="<?= ginny_esc($description) ?>">
    <link rel="icon" href="<?= ginny_esc(ginny_asset('images/favicon.png', $base)) ?>" type="image/png">
    <link rel="apple-touch-icon" href="<?= ginny_esc(ginny_asset('images/favicon.png', $base)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ginny_esc(ginny_asset('css/site.css', $base)) ?>">
</head>
<body>
<a class="skip-link" href="#main">Към съдържанието</a>
<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="<?= ginny_esc($home) ?>">
            <img src="<?= ginny_esc(ginny_asset('images/logo.png', $base)) ?>" alt="Ginny Rock Bar Sozopol logo" width="140" height="99">
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Меню">
            <span></span><span></span><span></span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="Основна навигация">
            <ul>
                <li><a href="<?= ginny_esc($home) ?>" class="<?= $active === 'home' ? 'is-active' : '' ?>">Начало</a></li>
                <li><a href="<?= ginny_esc(ginny_asset('programa/', $base)) ?>" class="<?= $active === 'programa' ? 'is-active' : '' ?>">Новини</a></li>
                <li><a href="<?= ginny_esc(ginny_asset('gallery/', $base)) ?>" class="<?= $active === 'gallery' ? 'is-active' : '' ?>">Моменти</a></li>
            </ul>
        </nav>
    </div>
</header>
<?php
}

/**
 * @param array{base?:string} $meta
 */
function ginny_footer(array $meta = []): void
{
    $base = $meta['base'] ?? '';
    ?>
<footer class="site-footer">
    <div class="footer-inner">
        <p class="footer-copy">Ginny Rock Bar · Созопол, Централен плаж · 24/7</p>
        <ul class="social">
            <li><a href="https://www.facebook.com/Ginny.Bar.Sozopol" target="_blank" rel="noopener noreferrer" aria-label="Facebook">Facebook</a></li>
            <li><a href="https://www.instagram.com/ginnybar.sozopol/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">Instagram</a></li>
        </ul>
    </div>
</footer>
<script src="<?= ginny_esc(ginny_asset('js/site.js', $base)) ?>" defer></script>
</body>
</html>
<?php
}
