<?php

namespace App\Http\Controllers;

use App\Models\ScheduleRunLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchedulerLogController extends Controller
{
    public function index(Request $request): View
    {
        $commandFilter = $request->string('command')->trim()->toString();

        $logs = ScheduleRunLog::query()
            ->when($commandFilter !== '', fn ($q) => $q->where('command', $commandFilter))
            ->latest('started_at')
            ->paginate(25)
            ->withQueryString();

        $commands = ScheduleRunLog::query()
            ->select('command')
            ->distinct()
            ->orderBy('command')
            ->pluck('command');

        return view('dashboard.scheduler', [
            'logs' => $logs,
            'commands' => $commands,
            'commandFilter' => $commandFilter,
        ]);
    }

    public function destroyAll(): RedirectResponse
    {
        ScheduleRunLog::query()->delete();

        return redirect()
            ->route('dashboard.scheduler')
            ->with('status', 'All schedule run logs have been deleted.');
    }
}
