<?php

declare(strict_types=1);

require __DIR__.'/lib/layout.php';
require __DIR__.'/lib/home.php';

$locale = 'bg';
$page = 'home';
$paths = kitchen_paths($locale, $page);

kitchen_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'home',
    'paths' => $paths,
    'title' => kitchen_t('meta_home_title', $locale),
    'description' => kitchen_t('meta_home_desc', $locale),
]);

kitchen_render_home($locale, $paths);

kitchen_footer([
    'locale' => $locale,
    'paths' => $paths,
]);
