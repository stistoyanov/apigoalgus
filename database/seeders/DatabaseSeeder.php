<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(MasterUserSeeder::class);
        $this->call(SystemUserSeeder::class);
        $this->call(TodorUserSeeder::class);
    }
}
