<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Queue tables are not used — all jobs run synchronously (QUEUE_CONNECTION=sync).
     */
    public function up(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
    }

    public function down(): void
    {
        // Intentionally empty — queue tables are not part of this application.
    }
};
