<?php

namespace App\Http\Controllers;

use App\Models\ScheduleRunLog;
use App\Support\Access;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $showSystemStats = Access::allowed($user, 'overview.system_stats');

        $lastHeartbeat = null;
        $totalScheduleLogs = null;

        if ($showSystemStats) {
            $lastHeartbeat = ScheduleRunLog::query()
                ->where('command', 'scheduler:heartbeat')
                ->latest('started_at')
                ->first();
            $totalScheduleLogs = ScheduleRunLog::query()->count();
        }

        return view('dashboard.index', [
            'user' => $user,
            'showSystemStats' => $showSystemStats,
            'lastHeartbeat' => $lastHeartbeat,
            'totalScheduleLogs' => $totalScheduleLogs,
        ]);
    }
}
