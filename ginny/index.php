<?php

declare(strict_types=1);

require __DIR__.'/lib/layout.php';
require __DIR__.'/lib/api-client.php';
require __DIR__.'/lib/home.php';

$locale = 'bg';
$page = 'home';
$paths = ginny_paths($locale, $page);
$payload = ginny_load_content($locale);

ginny_header([
    'locale' => $locale,
    'page' => $page,
    'active' => 'home',
    'paths' => $paths,
    'title' => ginny_c($payload, 'meta', 'title', ginny_t('meta_home_title', $locale)),
    'description' => ginny_c($payload, 'meta', 'description', ginny_t('meta_home_desc', $locale)),
    'payload' => $payload,
]);

ginny_render_home($locale, $paths, $payload);

ginny_footer([
    'locale' => $locale,
    'paths' => $paths,
    'payload' => $payload,
]);
