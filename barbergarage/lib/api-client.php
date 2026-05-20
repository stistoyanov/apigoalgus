<?php

/**
 * Server-side API client for the BarberGarage CMS.
 *
 * Loads JSON from api.goalgus.bg using a Bearer token, caches it for $cache_ttl
 * seconds on the local filesystem, and falls back to the last good cache when
 * the API is unreachable. Never exposes the Bearer token to the browser.
 */

declare(strict_types=1);

if (! function_exists('bg_config')) {
    function bg_config(): array
    {
        static $config = null;
        if ($config !== null) {
            return $config;
        }

        $path = dirname(__DIR__).'/config.php';
        if (! is_file($path)) {
            throw new RuntimeException(
                'Missing barbergarage/config.php. Copy config.sample.php to config.php and fill in api_token.'
            );
        }

        $config = require $path;
        if (! is_array($config)) {
            throw new RuntimeException('barbergarage/config.php must return an array.');
        }

        return $config;
    }
}

if (! function_exists('bg_cache_path')) {
    function bg_cache_path(string $locale): string
    {
        return dirname(__DIR__).'/cache/site-'.$locale.'.json';
    }
}

if (! function_exists('bg_fetch_remote')) {
    function bg_fetch_remote(string $locale): ?array
    {
        $config = bg_config();
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
        if (! is_array($data)) {
            return null;
        }

        return $data;
    }
}

if (! function_exists('bg_load_content')) {
    /**
     * Return the API payload for the given locale, with cache + stale fallback.
     *
     * @return array<string, mixed>
     */
    function bg_load_content(string $locale): array
    {
        $locale = $locale === 'en' ? 'en' : 'bg';
        $cacheFile = bg_cache_path($locale);
        $config = bg_config();
        $ttl = (int) ($config['cache_ttl'] ?? 60);

        $cacheFresh = is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl;

        if ($cacheFresh) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $remote = bg_fetch_remote($locale);

        if ($remote !== null) {
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

        throw new RuntimeException(
            'BarberGarage API unavailable and no cached content exists for locale "'.$locale.'".'
        );
    }
}
