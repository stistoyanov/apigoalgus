<?php

/**
 * Server-side API client for Ginny Rock Bar.
 * Bearer token stays server-only; stale cache is served when the API is down.
 */

declare(strict_types=1);

require_once __DIR__.'/i18n.php';
require_once __DIR__.'/helpers.php';

if (! function_exists('ginny_config')) {
    function ginny_config(): ?array
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

if (! function_exists('ginny_cache_path')) {
    function ginny_cache_path(string $locale): string
    {
        return dirname(__DIR__).'/cache/site-'.$locale.'.json';
    }
}

if (! function_exists('ginny_fetch_remote')) {
    function ginny_fetch_remote(string $locale): ?array
    {
        $config = ginny_config();
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

if (! function_exists('ginny_static_payload')) {
    /**
     * Offline / pre-CMS fallback built from local i18n + relative image paths.
     *
     * @return array<string, mixed>
     */
    function ginny_static_payload(string $locale): array
    {
        $locale = $locale === 'en' ? 'en' : 'bg';
        $base = '';

        return [
            'site' => ['slug' => 'ginny', 'name' => 'Ginny Rock Bar', 'domain' => 'ginny.bg', 'default_locale' => 'bg'],
            'locale' => $locale,
            'content' => [
                'meta' => [
                    'title' => ginny_t('meta_home_title', $locale),
                    'description' => ginny_t('meta_home_desc', $locale),
                    'news_title' => ginny_t('meta_news_title', $locale),
                    'news_desc' => ginny_t('meta_news_desc', $locale),
                    'gallery_title' => ginny_t('meta_gallery_title', $locale),
                    'gallery_desc' => ginny_t('meta_gallery_desc', $locale),
                ],
                'nav' => [
                    'home' => ginny_t('nav_home', $locale),
                    'news' => ginny_t('nav_news', $locale),
                    'gallery' => ginny_t('nav_gallery', $locale),
                    'kitchen' => ginny_t('nav_kitchen', $locale),
                ],
                'hero' => [
                    'tagline' => ginny_t('hero_tagline', $locale),
                    'cta_label' => ginny_t('hero_cta_label', $locale),
                ],
                'story' => [
                    'title' => ginny_t('story_title', $locale),
                    'p1' => ginny_t('story_p1', $locale),
                    'p2' => ginny_t('story_p2', $locale),
                    'p3' => ginny_t('story_p3', $locale),
                ],
                'rock' => [
                    'eyebrow' => ginny_t('rock_eyebrow', $locale),
                    'title' => ginny_t('rock_title', $locale),
                    'body' => ginny_t('rock_body', $locale),
                    'cta' => ginny_t('rock_cta', $locale),
                ],
                'card1' => ['title' => ginny_t('card1_title', $locale), 'body' => ginny_t('card1_body', $locale)],
                'card2' => ['title' => ginny_t('card2_title', $locale), 'body' => ginny_t('card2_body', $locale)],
                'card3' => ['title' => ginny_t('card3_title', $locale), 'body' => ginny_t('card3_body', $locale)],
                'drinks' => [
                    'eyebrow' => ginny_t('drinks_eyebrow', $locale),
                    'title' => ginny_t('drinks_title', $locale),
                    'body' => ginny_t('drinks_body', $locale),
                ],
                'visit' => [
                    'title' => ginny_t('visit_title', $locale),
                    'map_title' => ginny_t('map_title', $locale),
                    'loc' => ginny_t('visit_loc', $locale),
                    'loc_body' => ginny_t('visit_loc_body', $locale),
                    'book' => ginny_t('visit_book', $locale),
                    'hours' => ginny_t('visit_hours', $locale),
                    'hours_body' => ginny_t('visit_hours_body', $locale),
                ],
                'sister' => [
                    'eyebrow' => ginny_t('sister_eyebrow', $locale),
                    'title' => ginny_t('sister_title', $locale),
                    'body' => ginny_t('sister_body', $locale),
                    'cta' => ginny_t('sister_cta', $locale),
                ],
                'cheers' => [
                    'title' => ginny_t('cheers_title', $locale),
                    'cta' => ginny_t('cheers_cta', $locale),
                ],
                'footer' => ['line' => ginny_t('footer_line', $locale)],
                'news' => [
                    'h1' => ginny_t('news_h1', $locale),
                    'lead' => ginny_t('news_lead', $locale),
                ],
                'gallery' => [
                    'h1' => ginny_t('gallery_h1', $locale),
                    'lead' => ginny_t('gallery_lead', $locale),
                ],
                'lightbox' => [
                    'close' => ginny_t('lightbox_close', $locale),
                    'prev' => ginny_t('lightbox_prev', $locale),
                    'next' => ginny_t('lightbox_next', $locale),
                    'label' => ginny_t('lightbox_label', $locale),
                ],
            ],
            'settings' => [
                'phone' => '+359 88 606 0687',
                'phone_e164' => '+359886060687',
                'email' => 'ginny.bar@abv.bg',
                'facebook_url' => 'https://www.facebook.com/Ginny.Bar.Sozopol',
                'instagram_url' => 'https://www.instagram.com/ginnybar.sozopol/',
                'sister_url' => 'https://kitchen.ginny.bg/',
                'sister_url_en' => 'https://kitchen.ginny.bg/en/',
                'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.757173457178!2d27.691852212046342!3d42.422017871066956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40a6b875f66df0a7%3A0x75a128d5cf9df64f!2sGINNY%20BAR!5e1!3m2!1sen!2sbg!4v1725567648558!5m2!1sen!2sbg',
            ],
            'media' => [
                'hero_bg' => [['url' => 'images/hero.jpg']],
                'drinks' => [['url' => 'images/drinks.png']],
                'story_bg' => [['url' => 'images/story-bg.png']],
                'cheers_bg' => [['url' => 'images/cheers-bg.jpg']],
                'logo' => [['url' => 'images/logo.png']],
                'favicon' => [['url' => 'images/favicon.png']],
                'gallery' => array_map(
                    fn (array $p) => ['url' => $p['src'], 'alt' => $p['alt']],
                    ginny_gallery_photos($locale, $base),
                ),
            ],
            'posts' => array_map(function (array $p) {
                return [
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'posted_at' => null,
                    'date_label' => $p['date'],
                    'image_url' => $p['image'],
                    'permalink' => null,
                ];
            }, ginny_news_posts($locale, $base)),
            'menu' => ['brunch' => [], 'mains' => []],
            '_source' => 'static',
        ];
    }
}

if (! function_exists('ginny_load_content')) {
    /**
     * @return array<string, mixed>
     */
    function ginny_load_content(string $locale): array
    {
        $locale = $locale === 'en' ? 'en' : 'bg';
        $cacheFile = ginny_cache_path($locale);
        $config = ginny_config();
        $ttl = (int) ($config['cache_ttl'] ?? 60);

        if ($config !== null) {
            $cacheFresh = is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl;
            if ($cacheFresh) {
                $cached = json_decode((string) @file_get_contents($cacheFile), true);
                if (is_array($cached)) {
                    return $cached;
                }
            }

            $remote = ginny_fetch_remote($locale);
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

        return ginny_static_payload($locale);
    }
}

if (! function_exists('ginny_c')) {
    function ginny_c(array $payload, string $section, string $field, string $default = ''): string
    {
        $value = $payload['content'][$section][$field] ?? $default;

        return is_string($value) ? $value : $default;
    }
}

if (! function_exists('ginny_s')) {
    function ginny_s(array $payload, string $key, string $default = ''): string
    {
        $value = $payload['settings'][$key] ?? $default;
        if (is_bool($value) || is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return is_string($value) ? $value : $default;
    }
}

if (! function_exists('ginny_media_url')) {
    function ginny_media_url(array $payload, string $purpose, string $fallback = '', string $assetBase = ''): string
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
