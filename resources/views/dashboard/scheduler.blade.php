@extends('layouts.dashboard')

@section('title', 'Scheduler')

@section('content')
    <div class="panel">
        <h1>Scheduler runs</h1>
        <p class="lead">Database log of scheduled Artisan commands (proof-of-concept: <code>scheduler:heartbeat</code> every minute).</p>

        <form class="toolbar" method="GET" action="{{ route('dashboard.scheduler') }}">
            <label>
                Command
                <select name="command" onchange="this.form.submit()">
                    <option value="">All commands</option>
                    @foreach ($commands as $command)
                        <option value="{{ $command }}" @selected($commandFilter === $command)>{{ $command }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <form method="POST" action="{{ route('dashboard.scheduler.clear') }}" onsubmit="return confirm('Delete all schedule run logs?');" style="margin-bottom: 1.25rem;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete all records</button>
        </form>

        @if ($logs->isEmpty())
            <p>No schedule runs logged yet. Wait up to one minute for the heartbeat, or run <code>php artisan scheduler:heartbeat</code>.</p>
        @else
            <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Command</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Duration</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td><code>{{ $log->command }}</code></td>
                            <td>
                                <span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-failed' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td>{{ $log->started_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->duration_ms !== null ? $log->duration_ms.' ms' : '—' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($log->message, 80) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="pagination">
                @if ($logs->onFirstPage())
                    <span>&laquo; Prev</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}">&laquo; Prev</a>
                @endif

                <span class="current">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>

                @if ($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}">Next &raquo;</a>
                @else
                    <span>Next &raquo;</span>
                @endif
            </div>
        @endif
    </div>
@endsection
