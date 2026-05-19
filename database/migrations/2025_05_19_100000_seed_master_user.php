<?php

use Database\Seeders\MasterUserSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new MasterUserSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — master user is not removed on rollback.
    }
};
