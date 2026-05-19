<?php

namespace App\Services;

use App\Models\ScheduleRunLog;
use Throwable;

class ScheduleRunLogger
{
    /**
     * Run a callback and persist start/finish metadata for scheduler observability.
     *
     * @return mixed Callback return value
     */
    public function run(string $command, callable $callback, ?string $expression = null): mixed
    {
        $startedAt = microtime(true);

        $log = ScheduleRunLog::query()->create([
            'command' => $command,
            'expression' => $expression,
            'status' => ScheduleRunLog::STATUS_SUCCESS,
            'started_at' => now(),
        ]);

        try {
            $result = $callback();

            $log->update([
                'message' => is_string($result) ? $result : 'Completed successfully.',
                'finished_at' => now(),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            return $result;
        } catch (Throwable $e) {
            $log->update([
                'status' => ScheduleRunLog::STATUS_FAILED,
                'message' => $e->getMessage(),
                'exception' => $e->getTraceAsString(),
                'finished_at' => now(),
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);

            throw $e;
        }
    }
}
