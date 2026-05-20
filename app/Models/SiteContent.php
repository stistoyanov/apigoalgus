<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteContent extends Model
{
    public $timestamps = false;

    public const LOCALES = ['bg', 'en'];

    public const SECTIONS = [
        'meta', 'hero', 'welcome', 'about', 'gallery', 'video', 'hours', 'map', 'nav', 'footer', 'contact',
    ];

    protected $fillable = [
        'site_id',
        'locale',
        'section',
        'field',
        'value',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
