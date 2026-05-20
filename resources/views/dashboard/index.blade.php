@extends('layouts.dashboard')

@section('title', 'Overview')

@section('content')
    <div class="panel">
        <h1>Welcome, {{ $user->name }}</h1>
        <p class="lead">
            Signed in as <strong>{{ $user->email }}</strong>
            @if ($user->role)
                · <span class="role-pill">{{ $user->role->name }}</span>
            @endif
        </p>

        @if ($showSystemStats)
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
        @endif

        @php
            $links = collect([
                ['feature' => 'scheduler.view', 'route' => 'dashboard.scheduler', 'label' => 'Scheduler', 'desc' => 'Inspect scheduled command runs.'],
                ['feature' => 'logs.view', 'route' => 'dashboard.logs', 'label' => 'Logs', 'desc' => 'Browse Laravel log files.'],
                ['feature' => 'email_test', 'route' => 'dashboard.email', 'label' => 'Email test', 'desc' => 'Send a test message through the configured mailer.'],
                ['feature' => 'users.view', 'route' => 'dashboard.users', 'label' => 'Users', 'desc' => 'Manage administrator accounts and roles.'],
            ])->filter(fn ($l) => \App\Support\Access::allowed($user, $l['feature']));
        @endphp

        @if ($links->isNotEmpty())
            <h2 class="section-title">Available sections</h2>
            <div class="overview-links">
                @foreach ($links as $link)
                    <a class="overview-link" href="{{ route($link['route']) }}">
                        <strong>{{ $link['label'] }}</strong>
                        <span>{{ $link['desc'] }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p class="muted-block">No additional sections are available for your role. Your assigned access is read-only on this Overview page.</p>
        @endif
    </div>
@endsection
