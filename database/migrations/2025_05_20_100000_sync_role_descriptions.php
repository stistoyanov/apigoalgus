<?php

use Database\Seeders\RolesSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Re-runs RolesSeeder to keep names and descriptions in sync with
     * `.cursor/rules/aa-user-roles-rule.mdc`. Seeder is idempotent
     * (updateOrCreate keyed on `slug`).
     */
    public function up(): void
    {
        (new RolesSeeder)->run();
    }

    public function down(): void
    {
        // Intentionally empty — descriptions are not rolled back.
    }
};
