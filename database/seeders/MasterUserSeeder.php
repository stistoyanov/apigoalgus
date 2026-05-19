<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MasterUserSeeder extends Seeder
{
    public const EMAIL = 'stistoyanov@yahoo.com';

    public const PASSWORD = 'Slipknot1914!';

    public const NAME = 'Master Admin';

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => self::NAME,
                'password' => self::PASSWORD,
                'email_verified_at' => now(),
            ]
        );
    }
}
