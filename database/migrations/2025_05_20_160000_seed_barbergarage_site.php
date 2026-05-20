<?php

use Database\Seeders\BarbergarageSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new BarbergarageSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — seeded site data is not removed on rollback.
    }
};
