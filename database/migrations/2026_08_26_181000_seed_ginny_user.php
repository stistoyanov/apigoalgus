<?php

use Database\Seeders\GinnyUserSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new GinnyUserSeeder)->run();
    }

    public function down(): void
    {
        //
    }
};
