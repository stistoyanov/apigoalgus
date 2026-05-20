<?php

namespace App\Services\SiteContent;

use App\Models\Site;
use App\Models\SiteContent;
use App\Models\SiteMedia;
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
