<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('schedule_run_logs')) {
            $this->repairExistingTable();

            return;
        }

        Schema::create('schedule_run_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command', 191);
            $table->string('expression', 64)->nullable();
            $table->string('status', 20);
            $table->text('message')->nullable();
            $table->text('exception')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();

            $table->index(['command', 'started_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_run_logs');
    }

    /**
     * Production deploy may have created the table before the composite index failed
     * (utf8mb4 varchar(255) exceeds MySQL's 1000-byte index limit on shared hosting).
     */
    private function repairExistingTable(): void
    {
        DB::statement('ALTER TABLE `schedule_run_logs` MODIFY `command` VARCHAR(191) NOT NULL');

        $hasIndex = collect(DB::select('SHOW INDEX FROM `schedule_run_logs`'))
            ->contains(fn ($row) => ($row->Key_name ?? null) === 'schedule_run_logs_command_started_at_index');

        if (! $hasIndex) {
            Schema::table('schedule_run_logs', function (Blueprint $table) {
                $table->index(['command', 'started_at']);
            });
        }
    }
};
