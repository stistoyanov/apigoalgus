<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\SitePost;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitePostController extends Controller
{
    public function index(Site $site): View
    {
        $posts = SitePost::query()
            ->with('media')
            ->where('site_id', $site->id)
            ->orderBy('sort_order')
            ->orderByDesc('posted_at')
            ->orderByDesc('id')
            ->get();

        return view('dashboard.sites.posts', [
            'site' => $site,
            'posts' => $posts,
        ]);
    }

    public function store(Request $request, Site $site): RedirectResponse
    {
        $validated = $request->validate([
            'title_bg' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'excerpt_bg' => ['nullable', 'string', 'max:5000'],
            'excerpt_en' => ['nullable', 'string', 'max:5000'],
            'permalink' => ['nullable', 'url', 'max:500'],
            'posted_at' => ['nullable', 'date'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $maxSort = (int) SitePost::query()->where('site_id', $site->id)->max('sort_order');

        SitePost::query()->create([
            'site_id' => $site->id,
            'source' => SitePost::SOURCE_MANUAL,
            'sort_order' => $maxSort + 1,
            'is_published' => $request->boolean('is_published', true),
            'posted_at' => $validated['posted_at'] ?? now(),
            'title_bg' => $validated['title_bg'],
            'title_en' => $validated['title_en'],
            'excerpt_bg' => $validated['excerpt_bg'] ?? null,
            'excerpt_en' => $validated['excerpt_en'] ?? null,
            'permalink' => $validated['permalink'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        ActivityLogger::log(
            action: 'sites.posts.created',
            user: $request->user(),
            description: 'Created a news post for '.$site->name.'.',
            context: ['site' => $site->slug],
        );

        return redirect()
            ->route('dashboard.sites.posts', $site)
            ->with('status', 'Post created.');
    }

    public function update(Request $request, Site $site, SitePost $post): RedirectResponse
    {
        abort_unless($post->site_id === $site->id, 404);

        $validated = $request->validate([
            'title_bg' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'excerpt_bg' => ['nullable', 'string', 'max:5000'],
            'excerpt_en' => ['nullable', 'string', 'max:5000'],
            'permalink' => ['nullable', 'url', 'max:500'],
            'posted_at' => ['nullable', 'date'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $post->update([
            'title_bg' => $validated['title_bg'],
            'title_en' => $validated['title_en'],
            'excerpt_bg' => $validated['excerpt_bg'] ?? null,
            'excerpt_en' => $validated['excerpt_en'] ?? null,
            'permalink' => $validated['permalink'] ?? null,
            'posted_at' => $validated['posted_at'] ?? $post->posted_at,
            'image_url' => $validated['image_url'] ?? null,
            'sort_order' => $validated['sort_order'] ?? $post->sort_order,
            'is_published' => $request->boolean('is_published'),
        ]);

        ActivityLogger::log(
            action: 'sites.posts.updated',
            user: $request->user(),
            description: 'Updated a news post for '.$site->name.'.',
            context: ['site' => $site->slug, 'post_id' => $post->id],
        );

        return redirect()
            ->route('dashboard.sites.posts', $site)
            ->with('status', 'Post updated.');
    }

    public function destroy(Request $request, Site $site, SitePost $post): RedirectResponse
    {
        abort_unless($post->site_id === $site->id, 404);

        $post->delete();

        ActivityLogger::log(
            action: 'sites.posts.deleted',
            user: $request->user(),
            description: 'Deleted a news post for '.$site->name.'.',
            context: ['site' => $site->slug, 'post_id' => $post->id],
        );

        return redirect()
            ->route('dashboard.sites.posts', $site)
            ->with('status', 'Post deleted.');
    }
}
