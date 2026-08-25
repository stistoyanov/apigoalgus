<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';
require __DIR__.'/../lib/home.php';

$locale = 'en';
$page = 'home';
$paths = ginny_paths($locale, $page);

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'home',
    'paths' => $paths,
    'title' => ginny_t('meta_home_title', $locale),
    'description' => ginny_t('meta_home_desc', $locale),
]);

ginny_render_home($locale, $paths);

ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
]);
