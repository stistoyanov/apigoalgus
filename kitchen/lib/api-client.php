<?php

/**
 * Server-side API client for Ginny's Kitchen.
 * Bearer token stays server-only; stale cache is served when the API is down.
 */

declare(strict_types=1);

require_once __DIR__.'/i18n.php';
require_once __DIR__.'/helpers.php';

if (! function_exists('kitchen_config')) {
    function kitchen_config(): ?array
    {
        static $config = null;
        if ($config !== null) {
            return $config;
        }

        $path = dirname(__DIR__).'/config.php';
        if (! is_file($path)) {
            return null;
        }

        $loaded = require $path;
        $config = is_array($loaded) ? $loaded : null;

        return $config;
    }
}

if (! function_exists('kitchen_cache_path')) {
    function kitchen_cache_path(string $locale): string
    {
        return dirname(__DIR__).'/cache/site-'.$locale.'.json';
    }
}

if (! function_exists('kitchen_fetch_remote')) {
    function kitchen_fetch_remote(string $locale): ?array
    {
        $config = kitchen_config();
        if ($config === null) {
            return null;
        }

        $url = rtrim((string) ($config['api_base'] ?? ''), '/').'/api/v1/site/content/'.$locale;
        $token = (string) ($config['api_token'] ?? '');
        $timeout = (int) ($config['request_timeout'] ?? 5);

        if ($token === '' || $token === 'PASTE_RAW_TOKEN_HERE') {
            return null;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '.$token,
                'Accept: application/json',
            ],
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || ! is_string($body) || $body === '') {
            return null;
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : null;
    }
}

if (! function_exists('kitchen_static_payload')) {
    /**
     * Offline / pre-CMS fallback built from local i18n + relative image paths.
     *
     * @return array<string, mixed>
     */
    function kitchen_static_payload(string $locale): array
    {
        $locale = $locale === 'en' ? 'en' : 'bg';

        return [
            'site' => ['slug' => 'kitchen', 'name' => "Ginny's Kitchen", 'domain' => 'kitchen.ginny.bg', 'default_locale' => 'bg'],
            'locale' => $locale,
            'content' => [
                'meta' => [
                    'title' => kitchen_t('meta_home_title', $locale),
                    'description' => kitchen_t('meta_home_desc', $locale),
                    'menu_title' => kitchen_t('meta_menu_title', $locale),
                    'menu_desc' => kitchen_t('meta_menu_desc', $locale),
                ],
                'nav' => [
                    'home' => kitchen_t('nav_home', $locale),
                    'menu' => kitchen_t('nav_menu', $locale),
                    'bar' => kitchen_t('nav_bar', $locale),
                    'reserve' => kitchen_t('nav_reserve', $locale),
                ],
                'hero' => [
                    'tagline' => kitchen_t('hero_tagline', $locale),
                    'cta' => kitchen_t('hero_cta', $locale),
                ],
                'brunch' => [
                    'title' => kitchen_t('brunch_title', $locale),
                    'body' => kitchen_t('brunch_body', $locale),
                    'hours' => kitchen_t('brunch_hours', $locale),
                    'cta' => kitchen_t('brunch_cta', $locale),
                ],
                'story' => [
                    'eyebrow' => kitchen_t('story_eyebrow', $locale),
                    'title' => kitchen_t('story_title', $locale),
                    'h1' => kitchen_t('story_h1', $locale),
                    'p1' => kitchen_t('story_p1', $locale),
                    'p2' => kitchen_t('story_p2', $locale),
                    'h2' => kitchen_t('story_h2', $locale),
                    'p3' => kitchen_t('story_p3', $locale),
                    'h3' => kitchen_t('story_h3', $locale),
                    'p4' => kitchen_t('story_p4', $locale),
                ],
                'menu' => [
                    'eyebrow' => kitchen_t('menu_eyebrow', $locale),
                    'title' => kitchen_t('menu_title', $locale),
                    'cta' => kitchen_t('menu_cta', $locale),
                    'teaser1' => kitchen_t('teaser1', $locale),
                    'teaser2' => kitchen_t('teaser2', $locale),
                    'teaser3' => kitchen_t('teaser3', $locale),
                    'teaser4' => kitchen_t('teaser4', $locale),
                    'teaser1_image' => 'images/2025/04/freshtstart.webp',
                    'teaser2_image' => 'images/2025/04/morskahrana.webp',
                    'teaser3_image' => 'images/2025/04/burgeri-sozopol.webp',
                    'teaser4_image' => 'images/2025/04/desert.webp',
                    'h1' => kitchen_t('menu_h1', $locale),
                    'lead' => kitchen_t('menu_lead', $locale),
                    'brunch' => kitchen_t('menu_brunch', $locale),
                    'brunch_note' => kitchen_t('menu_brunch_note', $locale),
                    'mains' => kitchen_t('menu_mains', $locale),
                    'mains_note' => kitchen_t('menu_mains_note', $locale),
                    'see_you' => kitchen_t('menu_see_you', $locale),
                ],
                'promises' => [
                    'title' => kitchen_t('promises_title', $locale),
                    'p1_title' => kitchen_t('p1_title', $locale),
                    'p1_body' => kitchen_t('p1_body', $locale),
                    'p2_title' => kitchen_t('p2_title', $locale),
                    'p2_body' => kitchen_t('p2_body', $locale),
                    'p3_title' => kitchen_t('p3_title', $locale),
                    'p3_body' => kitchen_t('p3_body', $locale),
                    'p4_title' => kitchen_t('p4_title', $locale),
                    'p4_body' => kitchen_t('p4_body', $locale),
                    'p5_title' => kitchen_t('p5_title', $locale),
                    'p5_body' => kitchen_t('p5_body', $locale),
                    'p6_title' => kitchen_t('p6_title', $locale),
                    'p6_body' => kitchen_t('p6_body', $locale),
                ],
                'sister' => [
                    'eyebrow' => kitchen_t('sister_eyebrow', $locale),
                    'title' => kitchen_t('sister_title', $locale),
                    'body' => kitchen_t('sister_body', $locale),
                    'cta' => kitchen_t('sister_cta', $locale),
                ],
                'cta' => [
                    'title' => kitchen_t('cta_title', $locale),
                    'body' => kitchen_t('cta_body', $locale),
                    'btn' => kitchen_t('cta_btn', $locale),
                ],
                'footer' => [
                    'loc' => kitchen_t('footer_loc', $locale),
                    'hours_label' => kitchen_t('footer_hours_label', $locale),
                    'hours' => kitchen_t('footer_hours', $locale),
                    'book' => kitchen_t('footer_book', $locale),
                ],
            ],
            'settings' => [
                'phone' => '+359 88 606 0687',
                'phone_e164' => '+359886060687',
                'email' => 'ginnys.kitchen@abv.bg',
                'facebook_url' => 'https://www.facebook.com/Ginny.Bar.Sozopol',
                'instagram_url' => 'https://www.instagram.com/ginnybar.sozopol/',
                'sister_url' => 'https://ginny.bg/',
                'sister_url_en' => 'https://ginny.bg/en/',
            ],
            'media' => [
                'hero_bg' => [['url' => 'images/hero-bg.jpg']],
                'story_bg' => [['url' => 'images/story-bg.webp']],
                'about_photo' => [['url' => 'images/about-photo.jpg']],
                'logo' => [['url' => 'images/logo.png']],
                'favicon' => [['url' => 'images/favicon.png']],
            ],
            'posts' => [],
            'menu' => [
                'brunch' => array_map(
                    static fn (array $item): array => [
                        'title' => $item['title'],
                        'desc' => $item['desc'],
                        'image_url' => $item['img'] ?? null,
                    ],
                    kitchen_brunch_items($locale),
                ),
                'mains' => array_map(
                    static fn (array $item): array => [
                        'title' => $item['title'],
                        'desc' => $item['desc'],
                        'image_url' => $item['img'] ?? null,
                    ],
                    kitchen_main_items($locale, ''),
                ),
            ],
            '_source' => 'static',
        ];
    }
}

if (! function_exists('kitchen_load_content')) {
    /**
     * @return array<string, mixed>
     */
    function kitchen_load_content(string $locale): array
    {
        $locale = $locale === 'en' ? 'en' : 'bg';
        $cacheFile = kitchen_cache_path($locale);
        $config = kitchen_config();
        $ttl = (int) ($config['cache_ttl'] ?? 60);

        if ($config !== null) {
            $cacheFresh = is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl;
            if ($cacheFresh) {
                $cached = json_decode((string) @file_get_contents($cacheFile), true);
                if (is_array($cached)) {
                    return $cached;
                }
            }

            $remote = kitchen_fetch_remote($locale);
            if ($remote !== null) {
                @mkdir(dirname($cacheFile), 0755, true);
                $tmp = $cacheFile.'.tmp';
                if (@file_put_contents($tmp, json_encode($remote, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) !== false) {
                    @rename($tmp, $cacheFile);
                }

                return $remote;
            }

            if (is_file($cacheFile)) {
                $stale = json_decode((string) @file_get_contents($cacheFile), true);
                if (is_array($stale)) {
                    return $stale;
                }
            }
        }

        return kitchen_static_payload($locale);
    }
}

if (! function_exists('kitchen_c')) {
    function kitchen_c(array $payload, string $section, string $field, string $default = ''): string
    {
        $value = $payload['content'][$section][$field] ?? $default;

        return is_string($value) ? $value : $default;
    }
}

if (! function_exists('kitchen_s')) {
    function kitchen_s(array $payload, string $key, string $default = ''): string
    {
        $value = $payload['settings'][$key] ?? $default;
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return is_string($value) ? $value : $default;
    }
}

if (! function_exists('kitchen_media_url')) {
    function kitchen_media_url(array $payload, string $purpose, string $fallback = '', string $assetBase = ''): string
    {
        $items = $payload['media'][$purpose] ?? [];
        if (is_array($items) && isset($items[0]['url']) && is_string($items[0]['url']) && $items[0]['url'] !== '') {
            $url = $items[0]['url'];
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                return $url;
            }

            return $assetBase.ltrim($url, '/');
        }

        return $fallback !== '' ? $assetBase.ltrim($fallback, '/') : '';
    }
}
