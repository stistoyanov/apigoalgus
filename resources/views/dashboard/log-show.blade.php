@extends('layouts.dashboard')

@section('title', $filename)

@section('content')
    <div class="panel">
        <h1>{{ $filename }}</h1>
        <p class="lead">
            @if ($truncated)
                Showing last 500 lines (of {{ number_format($lines) }}@if($search) matching @endif).
            @else
                {{ number_format($lines) }} line(s)@if($search) matching search @endif.
            @endif
        </p>

        <form class="toolbar" method="GET" action="{{ route('dashboard.logs.show', $filename) }}">
            <label>
                Search
                <input type="search" name="search" value="{{ $search }}" placeholder="Filter lines…">
            </label>
            <button type="submit" class="btn btn-primary">Search</button>
            @if ($search)
                <a href="{{ route('dashboard.logs.show', $filename) }}" class="btn btn-outline">Clear search</a>
            @endif
        </form>

        <div style="margin-bottom: 1rem;">
            <a href="{{ route('dashboard.logs') }}" class="btn btn-outline">&larr; All logs</a>
            <form method="POST" action="{{ route('dashboard.logs.clear', $filename) }}" style="display:inline;" onsubmit="return confirm('Clear this log file?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Clear file</button>
            </form>
        </div>

        <pre class="log-preview">@if($content === ''){{ $search ? '(no matching lines)' : '(empty file)' }}@else{{ $content }}@endif</pre>
    </div>
@endsection
