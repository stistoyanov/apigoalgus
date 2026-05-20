<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function hasAnyRole(string ...$slugs): bool
    {
        $current = $this->role?->slug;

        return $current !== null && in_array($current, $slugs, true);
    }

    public function canAccess(string $feature): bool
    {
        return \App\Support\Access::allowed($this, $feature);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::SUPER_ADMIN);
    }

    public function isMaster(): bool
    {
        return $this->email === \Database\Seeders\MasterUserSeeder::EMAIL;
    }

    public function isSystem(): bool
    {
        return $this->email === \Database\Seeders\SystemUserSeeder::EMAIL;
    }

    /**
     * The built-in System user used to attribute background work.
     * Cached for the request to avoid re-querying on every log entry.
     */
    public static function system(): ?self
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached === false ? null : $cached;
        }

        $cached = self::query()->where('email', \Database\Seeders\SystemUserSeeder::EMAIL)->first() ?: false;

        return $cached === false ? null : $cached;
    }
}
