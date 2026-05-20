<?php

/**
 * BarberGarage runtime configuration.
 *
 * Copy this file to config.php and fill in the values.
 * config.php is gitignored — never commit it.
 *
 * @return array<string, mixed>
 */

return [
    // Base URL of the apigoalgus API, no trailing slash.
    'api_base' => 'https://api.goalgus.bg',

    // Bearer token created in /dashboard/sites/barbergarage/tokens.
    // Treat this like a password: it's a server-only secret.
    'api_token' => 'PASTE_RAW_TOKEN_HERE',

    // Cache TTL in seconds. The site keeps serving stale cache if the API is unreachable.
    'cache_ttl' => 60,

    // HTTP timeout when calling the API (seconds).
    'request_timeout' => 5,

    // Google Maps Embed key (kept on this side so the dashboard never sees it).
    'maps_api_key' => 'AIzaSyDbYFiwobgoEBRkLUW8OLxayv5Wp_ow4lw',
];
