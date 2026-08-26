<?php

declare(strict_types=1);

require __DIR__.'/../lib/layout.php';
require __DIR__.'/../lib/home.php';

$locale = 'en';
$page = 'home';
$paths = kitchen_paths($locale, $page);
$payload = kitchen_load_content($locale);

kitchen_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'home',
    'paths' => $paths,
    'title' => kitchen_c($payload, 'meta', 'title', kitchen_t('meta_home_title', $locale)),
    'description' => kitchen_c($payload, 'meta', 'description', kitchen_t('meta_home_desc', $locale)),
    'payload' => $payload,
]);

kitchen_render_home($locale, $paths, $payload);

kitchen_footer([
    'locale' => $locale,
    'paths' => $paths,
    'payload' => $payload,
]);
