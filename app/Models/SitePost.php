<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SitePost extends Model
{
    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_INSTAGRAM = 'instagram';

    protected $fillable = [
        'site_id',
        'source',
        'external_id',
        'sort_order',
        'is_published',
        'posted_at',
        'title_bg',
        'title_en',
        'excerpt_bg',
        'excerpt_en',
        'permalink',
        'media_id',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'posted_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(SiteMedia::class, 'media_id');
    }

    public function titleForLocale(string $locale): string
    {
        return (string) ($locale === 'en'
            ? ($this->title_en ?: $this->title_bg)
            : ($this->title_bg ?: $this->title_en));
    }

    public function excerptForLocale(string $locale): ?string
    {
        $value = $locale === 'en'
            ? ($this->excerpt_en ?: $this->excerpt_bg)
            : ($this->excerpt_bg ?: $this->excerpt_en);

        return $value !== null && $value !== '' ? (string) $value : null;
    }
}
