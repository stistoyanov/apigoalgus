<?php

declare(strict_types=1);

function ginny_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Asset path relative to the current page depth.
 * $base is '' for site root pages, '../' for one level deep.
 */
function ginny_asset(string $path, string $base = ''): string
{
    return $base.ltrim($path, '/');
}
