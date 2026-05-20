<?php

use Database\Seeders\SystemUserSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new SystemUserSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — System user is not removed on rollback.
    }
};
