<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class GinnyUserSeeder extends Seeder
{
    public const EMAIL = 'ginny.bar@abv.bg';

    public const PASSWORD = 'ginny.bar@';

    public const NAME = 'Ginny Bar';

    public function run(): void
    {
        $roleId = Role::query()->where('slug', 'admin')->value('id');

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
            'password' => self::PASSWORD,
            'role_id' => $roleId,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
