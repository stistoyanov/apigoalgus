<?php

use Database\Seeders\RolesSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->id();
                $table->string('slug', 64)->unique();
                $table->string('name', 100);
                $table->string('description', 500)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id')->nullable()->after('email');
                $table->index('role_id');
            });
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('role_id');
            });
        }

        $this->dropRoleIdForeignKeyIfPresent();

        if (Schema::hasTable('role_user')) {
            Schema::dropIfExists('role_user');
        }

        (new RolesSeeder)->run();
    }

    public function down(): void
    {
        $this->dropRoleIdForeignKeyIfPresent();

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropIndex(['role_id']);
                $table->dropColumn('role_id');
            }
        });

        Schema::dropIfExists('roles');
    }

    /**
     * Shared hosting (cPanel) often rejects FK creation (errno 150). We use an
     * index only; integrity is enforced in the application layer.
     */
    private function dropRoleIdForeignKeyIfPresent(): void
    {
        if (! Schema::hasColumn('users', 'role_id')) {
            return;
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
    }
};
