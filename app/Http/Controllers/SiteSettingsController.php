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
            'sister_url' => ['nullable', 'url', 'max:500'],
            'sister_url_en' => ['nullable', 'url', 'max:500'],
            'hours_label_bg' => ['nullable', 'string', 'max:120'],
            'hours_label_en' => ['nullable', 'string', 'max:120'],
            'brunch_hours_bg' => ['nullable', 'string', 'max:120'],
            'brunch_hours_en' => ['nullable', 'string', 'max:120'],
            'map_embed' => ['nullable', 'string', 'max:2000'],
            'map_lat' => ['nullable', 'numeric'],
            'map_lng' => ['nullable', 'numeric'],
            'map_zoom' => ['nullable', 'integer', 'min:1', 'max:21'],
            'gallery_cap' => ['nullable', 'integer', 'min:0', 'max:500'],
            'video_cap' => ['nullable', 'integer', 'min:0', 'max:50'],
            'instagram_user_id' => ['nullable', 'string', 'max:80'],
            'instagram_access_token' => ['nullable', 'string', 'max:500'],
            'hours' => ['nullable', 'array'],
            'hours.*.day_bg' => ['nullable', 'string', 'max:40'],
            'hours.*.day_en' => ['nullable', 'string', 'max:40'],
            'hours.*.hours' => ['nullable', 'string', 'max:40'],
            'hours.*.closed_bg' => ['nullable', 'string', 'max:40'],
            'hours.*.closed_en' => ['nullable', 'string', 'max:40'],
        ]);

        // Never wipe an existing Instagram token with a blank field.
        if (($validated['instagram_access_token'] ?? '') === '') {
            unset($validated['instagram_access_token']);
        }

        $this->repository->updateSettings($site, array_filter(
            $validated,
            fn ($value) => $value !== null,
        ));

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
