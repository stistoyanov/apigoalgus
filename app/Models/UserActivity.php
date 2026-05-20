<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'email',
        'action',
        'description',
        'context',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Friendly label for an action slug.
     * Centralised so the filter dropdown and the log rows agree.
     *
     * @return array<string, string>
     */
    public static function actionLabels(): array
    {
        return [
            'auth.login' => 'Logged in',
            'auth.login_failed' => 'Login failed',
            'auth.login_blocked' => 'Login blocked',
            'auth.logout' => 'Logged out',
            'files.uploaded' => 'File uploaded',
            'files.downloaded' => 'File downloaded',
            'files.public_downloaded' => 'Public link downloaded',
            'files.shared' => 'File share link created',
            'files.unshared' => 'File share link revoked',
            'files.deleted' => 'File deleted',
            'users.created' => 'User created',
            'users.updated' => 'User updated',
            'users.activated' => 'User activated',
            'users.deactivated' => 'User deactivated',
            'users.deleted' => 'User deleted',
            'sites.content.updated' => 'Site content updated',
            'sites.settings.updated' => 'Site settings updated',
            'sites.media.uploaded' => 'Site media uploaded',
            'sites.media.deleted' => 'Site media deleted',
            'sites.media.reordered' => 'Site media reordered',
            'sites.token.created' => 'Site API token created',
            'sites.token.revoked' => 'Site API token revoked',
            'api.site.content.fetched' => 'Site API content fetched',
            'api.site.auth_failed' => 'Site API auth failed',
            'api.site.media.fetched' => 'Site media fetched (signed URL)',
        ];
    }

    public function label(): string
    {
        return self::actionLabels()[$this->action] ?? $this->action;
    }
}
