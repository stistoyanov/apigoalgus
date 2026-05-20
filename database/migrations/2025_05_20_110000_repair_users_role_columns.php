<?php

use Database\Seeders\RolesSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs production if 2025_05_20_080000_create_roles_tables failed mid-way
 * (roles table exists but users.role_id FK could not be created).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        if (! Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable();
                $table->index('role_id');
            });
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }

        $database = Schema::getConnection()->getDatabaseName();
        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$database, 'users', 'FOREIGN KEY']
        );

        foreach ($constraints as $row) {
            if (str_contains($row->CONSTRAINT_NAME, 'role_id')) {
                DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `'.$row->CONSTRAINT_NAME.'`');
            }
        }

        (new RolesSeeder)->run();
    }

    public function down(): void
    {
        //
    }
};
