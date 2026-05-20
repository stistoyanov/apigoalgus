<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteContent;
use App\Services\SiteContent\SiteContentRepository;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    public function __construct(
        private SiteContentRepository $repository,
    ) {}

    public function edit(Request $request, Site $site): View
    {
        $locale = $request->string('locale')->trim()->toString();
        if (! in_array($locale, SiteContent::LOCALES, true)) {
            $locale = $site->default_locale;
        }

        return view('dashboard.sites.content', [
            'site' => $site,
            'locale' => $locale,
            'sections' => $this->repository->contentsForEditor($site, $locale),
            'sectionOrder' => SiteContent::SECTIONS,
        ]);
    }

    public function update(Request $request, Site $site): RedirectResponse
    {
        $locale = $request->string('locale')->trim()->toString();
        if (! in_array($locale, SiteContent::LOCALES, true)) {
            abort(422);
        }

        /** @var array<string, array<string, string|null>> $sections */
        $sections = $request->input('content', []);

        $this->repository->updateContent($site, $locale, $sections);

        ActivityLogger::log(
            action: 'sites.content.updated',
            user: $request->user(),
            description: 'Updated '.$site->name.' content ('.$locale.').',
            context: ['site' => $site->slug, 'locale' => $locale],
        );

        return redirect()
            ->route('dashboard.sites.content', ['site' => $site->slug, 'locale' => $locale])
            ->with('status', 'Content saved for '.strtoupper($locale).'.');
    }
}
