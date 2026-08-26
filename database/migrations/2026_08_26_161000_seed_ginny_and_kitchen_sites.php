<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new \Database\Seeders\GinnySeeder)->run();
        (new \Database\Seeders\KitchenSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — seeded site data is not removed on rollback.
    }
};
