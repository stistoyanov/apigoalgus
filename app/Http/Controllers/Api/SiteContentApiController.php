<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Services\SiteContent\SiteContentRepository;
use App\Support\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteContentApiController extends Controller
{
    public function __construct(
        private SiteContentRepository $repository,
    ) {}

    public function index(Request $request, ?string $locale = null): JsonResponse
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');
        $token = $request->attributes->get('site_token');

        $locale = $locale ?? $site->default_locale;

        if (! in_array($locale, ['bg', 'en'], true)) {
            return response()->json(['message' => 'Invalid locale.'], 422);
        }

        ActivityLogger::system(
            action: 'api.site.content.fetched',
            description: 'Site content fetched via API.',
            context: [
                'site' => $site->slug,
                'locale' => $locale,
                'token_prefix' => $token?->prefix,
                'ip' => $request->ip(),
            ],
        );

        return response()->json($this->repository->buildApiPayload($site, $locale));
    }
}
