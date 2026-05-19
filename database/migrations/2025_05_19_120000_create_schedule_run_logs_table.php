<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_run_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command');
            $table->string('expression')->nullable();
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
};
