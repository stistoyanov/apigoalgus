<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteMedia;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MediaSignedController extends Controller
{
    public function show(Request $request, Site $site, SiteMedia $media): Response
    {
        if ((int) $media->site_id !== (int) $site->id) {
            abort(404);
        }

        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($media->relativePath())) {
            abort(404, 'File missing on disk.');
        }

        $media->increment('download_count');

        ActivityLogger::system(
            action: 'api.site.media.fetched',
            description: 'Site media fetched via signed URL.',
            context: [
                'site' => $site->slug,
                'media_id' => $media->id,
                'purpose' => $media->purpose,
                'ip' => $request->ip(),
            ],
        );

        return $disk->response($media->relativePath(), $media->original_name);
    }
}
