<?php

declare(strict_types=1);

/**
 * Render helpers for the BarberGarage PHP shell.
 */

if (! function_exists('bg_e')) {
    function bg_e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}

if (! function_exists('bg_content')) {
    /**
     * @param  array<string, mixed>  $payload
     */
    function bg_content(array $payload, string $section, string $field, string $fallback = ''): string
    {
        $value = $payload['content'][$section][$field] ?? null;

        return is_string($value) && $value !== '' ? $value : $fallback;
    }
}

if (! function_exists('bg_setting')) {
    /**
     * @param  array<string, mixed>  $payload
     */
    function bg_setting(array $payload, string $key, $fallback = null)
    {
        return $payload['settings'][$key] ?? $fallback;
    }
}

if (! function_exists('bg_media_first')) {
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    function bg_media_first(array $payload, string $purpose): ?array
    {
        $items = $payload['media'][$purpose] ?? [];

        return is_array($items) && isset($items[0]) && is_array($items[0]) ? $items[0] : null;
    }
}

if (! function_exists('bg_media_featured')) {
    /**
     * Pick the featured video (or fall back to the first one).
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    function bg_media_featured(array $payload, string $purpose): ?array
    {
        $items = $payload['media'][$purpose] ?? [];
        if (! is_array($items) || empty($items)) {
            return null;
        }

        foreach ($items as $item) {
            if (is_array($item) && ! empty($item['is_featured'])) {
                return $item;
            }
        }

        return is_array($items[0]) ? $items[0] : null;
    }
}

if (! function_exists('bg_hours_rows')) {
    /**
     * @param  array<string, mixed>  $payload
     * @return list<array{day: string, value: string}>
     */
    function bg_hours_rows(array $payload, string $locale): array
    {
        $hours = $payload['settings']['hours'] ?? [];
        if (! is_array($hours)) {
            return [];
        }

        $rows = [];
        foreach ($hours as $row) {
            if (! is_array($row)) {
                continue;
            }
            $dayKey = $locale === 'en' ? 'day_en' : 'day_bg';
            $closedKey = $locale === 'en' ? 'closed_en' : 'closed_bg';
            $day = (string) ($row[$dayKey] ?? '');
            $value = (string) ($row['hours'] ?? '');
            if ($value === '') {
                $value = (string) ($row[$closedKey] ?? '');
            }
            $rows[] = ['day' => $day, 'value' => $value];
        }

        return $rows;
    }
}

if (! function_exists('bg_render_page')) {
    /**
     * Render the full HTML page from the API payload.
     *
     * Both index.php (BG) and en/index.php (EN) call this with the locale
     * and a $paths array that resolves relative URLs from the entry point.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $paths
     */
    function bg_render_page(array $payload, string $locale, array $paths): void
    {
        require __DIR__.'/template.php';
    }
}
