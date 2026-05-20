<?php

namespace App\Support;

use App\Models\User;

/**
 * Central access map.
 *
 * Keep this in sync with `.cursor/rules/aa-user-roles-rule.mdc` and the
 * `role:` middleware applied to dashboard routes.
 */
class Access
{
    /**
     * Feature slug → list of role slugs allowed to use it.
     *
     * @var array<string, list<string>>
     */
    public const FEATURES = [
        'dashboard.login' => ['super_admin', 'admin', 'manager', 'user', 'viewer', 'operator', 'support'],

        'overview.system_stats' => ['super_admin', 'admin', 'viewer', 'operator'],

        'scheduler.view' => ['super_admin', 'admin', 'viewer', 'operator'],
        'scheduler.clear' => ['super_admin', 'admin', 'operator'],

        'logs.view' => ['super_admin', 'admin', 'viewer', 'operator'],
        'logs.clear' => ['super_admin', 'admin', 'operator'],

        'email_test' => ['super_admin', 'admin'],

        'users.view' => ['super_admin', 'admin'],
        'users.manage' => ['super_admin', 'admin'],
        'users.manage_super_admins' => ['super_admin'],
    ];

    public static function allowed(?User $user, string $feature): bool
    {
        if (! $user || ! $user->is_active) {
            return false;
        }

        $slug = $user->role?->slug;
        if ($slug === null) {
            return false;
        }

        $allowed = self::FEATURES[$feature] ?? [];

        return in_array($slug, $allowed, true);
    }
}
