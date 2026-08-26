<?php

namespace App\Services\SiteContent;

use App\Models\Site;
use App\Models\SiteContent;
use App\Models\SiteMedia;
use App\Models\SiteMenuItem;
use App\Models\SitePost;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class SiteContentRepository
{
    public function __construct(
        private SiteMediaUrlBuilder $urlBuilder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function settingsMap(Site $site): array
    {
        return SiteSetting::query()
            ->where('site_id', $site->id)
            ->get()
            ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->value])
            ->all();
    }

    /**
     * @return array<string, array<string, string|null>>
     */
    public function contentMap(Site $site, string $locale): array
    {
        $rows = SiteContent::query()
            ->where('site_id', $site->id)
            ->where('locale', $locale)
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->section][$row->field] = $row->value;
        }

        return $map;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildApiPayload(Site $site, string $locale): array
    {
        $settings = $this->settingsMap($site);
        $content = $this->contentMap($site, $locale);

        return [
            'site' => [
                'slug' => $site->slug,
                'name' => $site->name,
                'domain' => $site->domain,
                'default_locale' => $site->default_locale,
            ],
            'locale' => $locale,
            'content' => $content,
            'settings' => $settings,
            'media' => $this->mediaPayload($site, $locale),
            'posts' => $this->postsPayload($site, $locale),
            'menu' => $this->menuPayload($site, $locale),
        ];
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function mediaPayload(Site $site, string $locale): array
    {
        $items = SiteMedia::query()
            ->where('site_id', $site->id)
            ->orderBy('purpose')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = [
            'gallery' => [],
            'video' => [],
            'about_photo' => [],
            'hero_bg' => [],
            'logo' => [],
            'favicon' => [],
            'menu' => [],
            'drinks' => [],
            'story_bg' => [],
            'cheers_bg' => [],
        ];

        foreach ($items as $media) {
            $entry = [
                'id' => $media->id,
                'kind' => $media->kind,
                'purpose' => $media->purpose,
                'sort_order' => $media->sort_order,
                'original_name' => $media->original_name,
                'mime_type' => $media->mime_type,
                'size_bytes' => (int) $media->size_bytes,
                'alt' => $media->altForLocale($locale),
                'is_featured' => (bool) $media->is_featured,
                'url' => $this->urlBuilder->signedUrl($site, $media),
            ];

            if (isset($grouped[$media->purpose])) {
                $grouped[$media->purpose][] = $entry;
            }
        }

        return $grouped;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function postsPayload(Site $site, string $locale): array
    {
        return SitePost::query()
            ->with('media')
            ->where('site_id', $site->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('posted_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (SitePost $post) use ($site, $locale) {
                $imageUrl = $post->image_url;
                if ($post->media) {
                    $imageUrl = $this->urlBuilder->signedUrl($site, $post->media);
                }

                return [
                    'id' => $post->id,
                    'source' => $post->source,
                    'external_id' => $post->external_id,
                    'posted_at' => optional($post->posted_at)?->toIso8601String(),
                    'title' => $post->titleForLocale($locale),
                    'excerpt' => $post->excerptForLocale($locale),
                    'permalink' => $post->permalink,
                    'image_url' => $imageUrl,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array{brunch: list<array<string, mixed>>, mains: list<array<string, mixed>>}
     */
    public function menuPayload(Site $site, string $locale): array
    {
        $items = SiteMenuItem::query()
            ->with('media')
            ->where('site_id', $site->id)
            ->where('is_published', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $out = [
            'brunch' => [],
            'mains' => [],
        ];

        foreach ($items as $item) {
            $entry = [
                'id' => $item->id,
                'category' => $item->category,
                'title' => $item->titleForLocale($locale),
                'desc' => $item->descForLocale($locale),
                'image_url' => $item->media
                    ? $this->urlBuilder->signedUrl($site, $item->media)
                    : null,
            ];

            if (isset($out[$item->category])) {
                $out[$item->category][] = $entry;
            }
        }

        return $out;
    }

    /**
     * @param  array<string, array<string, string|null>>  $sections
     */
    public function updateContent(Site $site, string $locale, array $sections): void
    {
        foreach ($sections as $section => $fields) {
            if (! is_array($fields)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                SiteContent::query()->updateOrCreate(
                    [
                        'site_id' => $site->id,
                        'locale' => $locale,
                        'section' => $section,
                        'field' => (string) $field,
                    ],
                    ['value' => $value],
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function updateSettings(Site $site, array $settings): void
    {
        foreach ($settings as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['site_id' => $site->id, 'key' => (string) $key],
                ['value' => $value],
            );
        }
    }

    /**
     * Content fields grouped by section for the dashboard editor.
     *
     * @return Collection<string, Collection<int, SiteContent>>
     */
    public function contentsForEditor(Site $site, string $locale): Collection
    {
        return SiteContent::query()
            ->where('site_id', $site->id)
            ->where('locale', $locale)
            ->orderBy('section')
            ->orderBy('field')
            ->get()
            ->groupBy('section');
    }
}
