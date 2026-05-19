<?php

namespace App\Http\Controllers;

use App\Models\ScheduleRunLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lastHeartbeat = ScheduleRunLog::query()
            ->where('command', 'scheduler:heartbeat')
            ->latest('started_at')
            ->first();

        return view('dashboard.index', [
            'user' => auth()->user(),
            'lastHeartbeat' => $lastHeartbeat,
            'totalScheduleLogs' => ScheduleRunLog::query()->count(),
        ]);
    }
}
