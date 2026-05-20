<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteMedia extends Model
{
    public const KIND_IMAGE = 'image';

    public const KIND_VIDEO = 'video';

    public const PURPOSE_GALLERY = 'gallery';

    public const PURPOSE_VIDEO = 'video';

    public const PURPOSE_ABOUT_PHOTO = 'about_photo';

    public const PURPOSE_HERO_BG = 'hero_bg';

    public const PURPOSE_LOGO = 'logo';

    public const PURPOSE_FAVICON = 'favicon';

    protected $fillable = [
        'site_id',
        'kind',
        'purpose',
        'sort_order',
        'original_name',
        'stored_name',
        'mime_type',
        'size_bytes',
        'alt_text_bg',
        'alt_text_en',
        'is_featured',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'size_bytes' => 'integer',
            'is_featured' => 'boolean',
            'download_count' => 'integer',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function relativePath(): string
    {
        $folder = $this->kind === self::KIND_VIDEO ? 'videos' : 'images';
        $slug = $this->relationLoaded('site')
            ? $this->site->slug
            : Site::query()->whereKey($this->site_id)->value('slug');

        return 'sites/'.$slug.'/'.$folder.'/'.$this->stored_name;
    }

    public function altForLocale(string $locale): ?string
    {
        return $locale === 'en' ? $this->alt_text_en : $this->alt_text_bg;
    }
}
