<?php

namespace App\Console\Commands;

use App\Services\ScheduleRunLogger;
use Illuminate\Console\Command;

class SchedulerHeartbeatCommand extends Command
{
    protected $signature = 'scheduler:heartbeat';

    protected $description = 'Proof-of-concept scheduled task that writes a row to schedule_run_logs';

    public function handle(ScheduleRunLogger $logger): int
    {
        $message = $logger->run(
            'scheduler:heartbeat',
            fn () => 'Scheduler heartbeat at '.now()->toDateTimeString(),
            '* * * * *'
        );

        $this->info($message);

        return self::SUCCESS;
    }
}
