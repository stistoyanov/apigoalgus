<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\SiteContent\SiteContentRepository;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function __construct(
        private SiteContentRepository $repository,
    ) {}

    public function edit(Site $site): View
    {
        return view('dashboard.sites.settings', [
            'site' => $site,
            'settings' => $this->repository->settingsMap($site),
        ]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
            'phone_e164' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'address_bg' => ['required', 'string', 'max:500'],
            'address_en' => ['required', 'string', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'map_lat' => ['required', 'numeric'],
            'map_lng' => ['required', 'numeric'],
            'map_zoom' => ['required', 'integer', 'min:1', 'max:21'],
            'gallery_cap' => ['required', 'integer', 'min:1', 'max:500'],
            'video_cap' => ['required', 'integer', 'min:1', 'max:50'],
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.day_bg' => ['required', 'string', 'max:40'],
            'hours.*.day_en' => ['required', 'string', 'max:40'],
            'hours.*.hours' => ['nullable', 'string', 'max:40'],
            'hours.*.closed_bg' => ['nullable', 'string', 'max:40'],
            'hours.*.closed_en' => ['nullable', 'string', 'max:40'],
        ]);

        $this->repository->updateSettings($site, $validated);

        ActivityLogger::log(
            action: 'sites.settings.updated',
            user: $request->user(),
            description: 'Updated '.$site->name.' settings.',
            context: ['site' => $site->slug],
        );

        return redirect()
            ->route('dashboard.sites.settings', $site)
            ->with('status', 'Settings saved.');
    }
}
