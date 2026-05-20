<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates / keeps in sync the built-in System user.
 *
 * The System user has the `service_account` role and is the actor recorded
 * against background work (scheduled commands, queue jobs). It can never
 * sign in to the dashboard — `LoginController` rejects `service_account`
 * users — and the UserController treats it like the master user (cannot be
 * edited, deactivated, deleted, or moved to another role).
 *
 * Run via migration `2025_05_20_140000_seed_system_user.php`. Idempotent
 * (`updateOrCreate` keyed on email), so re-runs only enforce the role/active
 * flag, never replace the password unless the row is missing.
 */
class SystemUserSeeder extends Seeder
{
    public const EMAIL = 'system@api.goalgus.bg';

    public const NAME = 'System';

    public function run(): void
    {
        $roleId = Role::query()->where('slug', Role::SERVICE_ACCOUNT)->value('id');

        $existing = User::query()->where('email', self::EMAIL)->first();

        if ($existing) {
            $existing->name = self::NAME;
            if ($roleId !== null) {
                $existing->role_id = $roleId;
            }
            $existing->is_active = true;
            $existing->save();

            return;
        }

        User::query()->create([
            'email' => self::EMAIL,
            'name' => self::NAME,
            'password' => Str::random(64),
            'role_id' => $roleId,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
