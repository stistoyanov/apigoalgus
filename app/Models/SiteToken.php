<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SiteToken extends Model
{
    protected $fillable = [
        'site_id',
        'name',
        'token_hash',
        'token_encrypted',
        'prefix',
        'last_used_at',
        'expires_at',
        'revoked_at',
    ];

    protected $hidden = [
        'token_encrypted',
        'token_hash',
    ];

    protected function casts(): array
    {
        return [
            'token_encrypted' => 'encrypted',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function isActive(): bool
    {
        if ($this->revoked_at !== null) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public static function hashToken(string $raw): string
    {
        return hash('sha256', $raw);
    }

    /**
     * @return array{token: SiteToken, raw: string}
     */
    public static function createForSite(Site $site, string $name): array
    {
        $raw = Str::random(64);

        $token = static::query()->create([
            'site_id' => $site->id,
            'name' => $name,
            'token_hash' => self::hashToken($raw),
            'token_encrypted' => $raw,
            'prefix' => substr($raw, 0, 8),
        ]);

        return ['token' => $token, 'raw' => $raw];
    }

    public function rawToken(): ?string
    {
        return $this->token_encrypted;
    }
}
