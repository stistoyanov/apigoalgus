<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiteController extends Controller
{
    public function index(): View
    {
        $sites = Site::query()->orderBy('name')->get();

        return view('dashboard.sites.index', [
            'sites' => $sites,
        ]);
    }

    public function show(Site $site): RedirectResponse
    {
        return redirect()->route('dashboard.sites.content', ['site' => $site->slug, 'locale' => $site->default_locale]);
    }
}
