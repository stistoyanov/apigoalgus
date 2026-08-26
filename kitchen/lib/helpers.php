<?php

declare(strict_types=1);

require_once __DIR__.'/i18n.php';

function kitchen_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Asset path relative to the current page depth.
 */
function kitchen_asset(string $path, string $base = ''): string
{
    return $base.ltrim($path, '/');
}

/** Absolute URL to the sister Rock Bar site for the current locale. */
function kitchen_bar_url(string $locale = 'bg'): string
{
    return $locale === 'en'
        ? 'https://ginny.bg/en/'
        : 'https://ginny.bg/';
}

/**
 * @return array{locale:string,base:string,home:string,menu:string,lang_switch:string,lang_other:string}
 */
function kitchen_paths(string $locale, string $page = 'home'): array
{
    $locale = $locale === 'en' ? 'en' : 'bg';

    if ($locale === 'en') {
        if ($page === 'home') {
            return [
                'locale' => 'en',
                'base' => '../',
                'home' => './',
                'menu' => 'ginnys-menu/',
                'lang_switch' => '../',
                'lang_other' => 'BG',
            ];
        }

        return [
            'locale' => 'en',
            'base' => '../../',
            'home' => '../',
            'menu' => './',
            'lang_switch' => '../../ginnys-menu/',
            'lang_other' => 'BG',
        ];
    }

    if ($page === 'home') {
        return [
            'locale' => 'bg',
            'base' => '',
            'home' => './',
            'menu' => 'ginnys-menu/',
            'lang_switch' => 'en/',
            'lang_other' => 'EN',
        ];
    }

    return [
        'locale' => 'bg',
        'base' => '../',
        'home' => '../',
        'menu' => './',
        'lang_switch' => '../en/ginnys-menu/',
        'lang_other' => 'EN',
    ];
}
