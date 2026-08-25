<?php

declare(strict_types=1);

function kitchen_esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function kitchen_asset(string $path, string $base = ''): string
{
    return $base.ltrim($path, '/');
}
