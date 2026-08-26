<?php

declare(strict_types=1);

require_once __DIR__.'/i18n.php';

function ginny_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Asset path relative to the current page depth.
 * $base is '' for site root pages, '../' for one level deep, '../../' for /en/programa/.
 */
function ginny_asset(string $path, string $base = ''): string
{
    return $base.ltrim($path, '/');
}

/** Absolute URL to the sister Kitchen site for the current locale. */
function ginny_kitchen_url(string $locale = 'bg'): string
{
    return $locale === 'en'
        ? 'https://kitchen.ginny.bg/en/'
        : 'https://kitchen.ginny.bg/';
}

/**
 * Paths for a given locale and depth.
 *
 * @return array{locale:string,base:string,home:string,news:string,gallery:string,lang_switch:string,lang_other:string}
 */
function ginny_paths(string $locale, string $page = 'home'): array
{
    $locale = $locale === 'en' ? 'en' : 'bg';

    if ($locale === 'en') {
        if ($page === 'home') {
            return [
                'locale' => 'en',
                'base' => '../',
                'home' => './',
                'news' => 'programa/',
                'gallery' => 'gallery/',
                'lang_switch' => '../',
                'lang_other' => 'BG',
            ];
        }

        // en/programa or en/gallery
        return [
            'locale' => 'en',
            'base' => '../../',
            'home' => '../',
            'news' => '../programa/',
            'gallery' => '../gallery/',
            'lang_switch' => $page === 'programa' ? '../../programa/' : '../../gallery/',
            'lang_other' => 'BG',
        ];
    }

    // BG
    if ($page === 'home') {
        return [
            'locale' => 'bg',
            'base' => '',
            'home' => './',
            'news' => 'programa/',
            'gallery' => 'gallery/',
            'lang_switch' => 'en/',
            'lang_other' => 'EN',
        ];
    }

    return [
        'locale' => 'bg',
        'base' => '../',
        'home' => '../',
        'news' => '../programa/',
        'gallery' => '../gallery/',
        'lang_switch' => $page === 'programa' ? '../en/programa/' : '../en/gallery/',
        'lang_other' => 'EN',
    ];
}
