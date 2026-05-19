@extends('layouts.dashboard')

@section('title', 'Overview')

@section('content')
    <div class="panel">
        <h1>Dashboard</h1>
        <p class="lead">Signed in as {{ $user->email }}</p>

        <div class="meta-grid">
            <div class="meta-card">
                <strong>Schedule log entries</strong>
                {{ number_format($totalScheduleLogs) }}
            </div>
            <div class="meta-card">
                <strong>Last heartbeat</strong>
                @if ($lastHeartbeat)
                    {{ $lastHeartbeat->started_at->diffForHumans() }}
                @else
                    Not recorded yet
                @endif
            </div>
        </div>

        @if ($lastHeartbeat && $lastHeartbeat->started_at->lt(now()->subMinutes(2)))
            <div class="status-banner warn">
                No heartbeat in the last 2 minutes. Ensure the scheduler container is running locally
                (<code>docker compose up scheduler</code>) or cron runs <code>schedule:run</code> on production.
            </div>
        @elseif ($lastHeartbeat)
            <div class="status-banner">
                Scheduler heartbeat is active. Last run: {{ $lastHeartbeat->started_at->format('Y-m-d H:i:s') }}.
            </div>
        @endif

        <p>
            Use <a href="{{ route('dashboard.scheduler') }}">Scheduler</a> to inspect scheduled command runs,
            <a href="{{ route('dashboard.logs') }}">Logs</a> to browse Laravel log files, or
            <a href="{{ route('dashboard.email') }}">Email test</a> to verify outbound mail.
        </p>
    </div>
@endsection
