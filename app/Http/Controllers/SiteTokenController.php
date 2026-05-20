<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteToken;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteTokenController extends Controller
{
    public function index(Site $site): View
    {
        return view('dashboard.sites.tokens', [
            'site' => $site,
            'tokens' => $site->tokens()->orderByDesc('id')->get(),
            'rawToken' => session('raw_site_token'),
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $result = SiteToken::createForSite($site, $validated['name']);

        ActivityLogger::log(
            action: 'sites.token.created',
            user: $request->user(),
            description: 'Created API token "'.$validated['name'].'" for '.$site->name.'.',
            context: ['site' => $site->slug, 'token_prefix' => $result['token']->prefix],
        );

        return redirect()
            ->route('dashboard.sites.tokens', $site)
            ->with('status', 'Token created. Copy it now — it will not be shown again.')
            ->with('raw_site_token', $result['raw']);
    }

    public function revoke(Request $request, Site $site, SiteToken $token): RedirectResponse
    {
        if ((int) $token->site_id !== (int) $site->id) {
            abort(404);
        }

        if ($token->revoked_at === null) {
            $token->revoked_at = now();
            $token->save();
        }

        ActivityLogger::log(
            action: 'sites.token.revoked',
            user: $request->user(),
            description: 'Revoked API token '.$token->prefix.'… for '.$site->name.'.',
            context: ['site' => $site->slug, 'token_prefix' => $token->prefix],
        );

        return back()->with('status', 'Token revoked.');
    }
}
