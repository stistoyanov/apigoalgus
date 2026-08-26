<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SiteMenuItem;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SiteMenuItemController extends Controller
{
    public function index(Site $site): View
    {
        $items = SiteMenuItem::query()
            ->with('media')
            ->where('site_id', $site->id)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('category');

        return view('dashboard.sites.menu', [
            'site' => $site,
            'items' => $items,
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', Rule::in([SiteMenuItem::CATEGORY_BRUNCH, SiteMenuItem::CATEGORY_MAINS])],
            'title_bg' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'desc_bg' => ['nullable', 'string', 'max:5000'],
            'desc_en' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $maxSort = (int) SiteMenuItem::query()
            ->where('site_id', $site->id)
            ->where('category', $validated['category'])
            ->max('sort_order');

        SiteMenuItem::query()->create([
            'site_id' => $site->id,
            'category' => $validated['category'],
            'sort_order' => $maxSort + 1,
            'is_published' => $request->boolean('is_published', true),
            'title_bg' => $validated['title_bg'],
            'title_en' => $validated['title_en'],
            'desc_bg' => $validated['desc_bg'] ?? null,
            'desc_en' => $validated['desc_en'] ?? null,
        ]);

        ActivityLogger::log(
            action: 'sites.menu.created',
            user: $request->user(),
            description: 'Created a menu item for '.$site->name.'.',
            context: ['site' => $site->slug, 'category' => $validated['category']],
        );

        return redirect()
            ->route('dashboard.sites.menu', $site)
            ->with('status', 'Menu item created.');
    }

    public function update(Request $request, Site $site, SiteMenuItem $item): RedirectResponse
    {
        abort_unless($item->site_id === $site->id, 404);

        $validated = $request->validate([
            'category' => ['required', Rule::in([SiteMenuItem::CATEGORY_BRUNCH, SiteMenuItem::CATEGORY_MAINS])],
            'title_bg' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'desc_bg' => ['nullable', 'string', 'max:5000'],
            'desc_en' => ['nullable', 'string', 'max:5000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $item->update([
            'category' => $validated['category'],
            'title_bg' => $validated['title_bg'],
            'title_en' => $validated['title_en'],
            'desc_bg' => $validated['desc_bg'] ?? null,
            'desc_en' => $validated['desc_en'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $item->sort_order,
            'is_published' => $request->boolean('is_published'),
        ]);

        ActivityLogger::log(
            action: 'sites.menu.updated',
            user: $request->user(),
            description: 'Updated a menu item for '.$site->name.'.',
            context: ['site' => $site->slug, 'item_id' => $item->id],
        );

        return redirect()
            ->route('dashboard.sites.menu', $site)
            ->with('status', 'Menu item updated.');
    }

    public function destroy(Request $request, Site $site, SiteMenuItem $item): RedirectResponse
    {
        abort_unless($item->site_id === $site->id, 404);

        $item->delete();

        ActivityLogger::log(
            action: 'sites.menu.deleted',
            user: $request->user(),
            description: 'Deleted a menu item for '.$site->name.'.',
            context: ['site' => $site->slug, 'item_id' => $item->id],
        );

        return redirect()
            ->route('dashboard.sites.menu', $site)
            ->with('status', 'Menu item deleted.');
    }
}
