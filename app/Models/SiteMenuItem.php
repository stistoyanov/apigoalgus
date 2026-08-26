<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteMenuItem extends Model
{
    public const CATEGORY_BRUNCH = 'brunch';

    public const CATEGORY_MAINS = 'mains';

    protected $fillable = [
        'site_id',
        'category',
        'sort_order',
        'is_published',
        'title_bg',
        'title_en',
        'desc_bg',
        'desc_en',
        'media_id',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
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

    public function descForLocale(string $locale): ?string
    {
        $value = $locale === 'en'
            ? ($this->desc_en ?: $this->desc_bg)
            : ($this->desc_bg ?: $this->desc_en);

        return $value !== null && $value !== '' ? (string) $value : null;
    }
}
