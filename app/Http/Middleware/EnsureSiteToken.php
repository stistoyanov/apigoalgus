<?php

namespace App\Http\Middleware;

use App\Models\Site;
use App\Models\SiteToken;
use App\Support\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $raw = $this->extractBearerToken($request);

        if ($raw === null) {
            $this->logFailure($request, 'missing_token');

            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $token = SiteToken::query()
            ->where('token_hash', SiteToken::hashToken($raw))
            ->first();

        if (! $token || ! $token->isActive()) {
            $this->logFailure($request, 'invalid_token', $token?->prefix);

            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $site = Site::query()->find($token->site_id);

        if (! $site || ! $site->is_active) {
            $this->logFailure($request, 'inactive_site', $token->prefix);

            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        $request->attributes->set('site', $site);
        $request->attributes->set('site_token', $token);

        return $next($request);
    }

    private function extractBearerToken(Request $request): ?string
    {
        $header = $request->header('Authorization', '');

        if (preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function logFailure(Request $request, string $reason, ?string $prefix = null): void
    {
        ActivityLogger::system(
            action: 'api.site.auth_failed',
            description: 'Site API authentication failed.',
            context: [
                'reason' => $reason,
                'token_prefix' => $prefix,
                'ip' => $request->ip(),
                'path' => $request->path(),
            ],
        );
    }
}
