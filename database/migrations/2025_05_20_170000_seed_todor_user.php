<?php

use Database\Seeders\TodorUserSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new TodorUserSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — Todor user is not removed on rollback.
    }
};
