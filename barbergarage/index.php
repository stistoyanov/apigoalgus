<?php

declare(strict_types=1);

require __DIR__.'/lib/api-client.php';
require __DIR__.'/lib/render.php';

try {
    $payload = bg_load_content('bg');
} catch (Throwable $e) {
    http_response_code(503);
    header('Retry-After: 60');
    echo '<!doctype html><meta charset="utf-8"><title>Barbergarage</title>'
        .'<p style="font-family:sans-serif;padding:2rem">Сайтът временно е недостъпен. Моля, опитайте отново след малко.</p>';
    error_log('barbergarage: '.$e->getMessage());

    return;
}

bg_render_page($payload, 'bg', [
    'assets' => '',
    'home_url' => './',
    'en_url' => 'en/',
    'lang_link_url' => 'en/',
]);
