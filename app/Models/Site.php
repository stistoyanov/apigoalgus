<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    public const SLUG_BARBERGARAGE = 'barbergarage';

    protected $fillable = [
        'slug',
        'name',
        'domain',
        'default_locale',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(SiteToken::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(SiteSetting::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(SiteContent::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(SiteMedia::class);
    }

    public static function barbergarage(): ?self
    {
        return static::query()->where('slug', self::SLUG_BARBERGARAGE)->first();
    }
}
