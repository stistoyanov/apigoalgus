<?php

namespace App\Services\SiteContent;

use App\Models\Site;
use App\Models\SiteMedia;
use Illuminate\Support\Facades\URL;

class SiteMediaUrlBuilder
{
    public function signedUrl(Site $site, SiteMedia $media, ?int $ttlHours = 24): string
    {
        return URL::signedRoute(
            'sites.media',
            ['site' => $site->slug, 'media' => $media->id],
            now()->addHours($ttlHours),
        );
    }
}
