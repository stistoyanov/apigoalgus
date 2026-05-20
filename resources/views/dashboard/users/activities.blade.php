@extends('layouts.dashboard')

@section('title', 'User Actions')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>Users</h1>
                <p class="lead">Audit log of authentication, file activity, and user management.</p>
            </div>
        </div>

        @include('dashboard.users._tabs')

        <form class="toolbar" method="GET" action="{{ route('dashboard.users.activities') }}">
            <label>
                User
                <select name="user_id" onchange="this.form.submit()">
                    <option value="">All users</option>
                    @foreach ($usersForFilter as $u)
                        <option value="{{ $u->id }}" @selected($userFilter === $u->id)>
                            {{ $u->name }}@if ($u->isSystem()) — System account@endif ({{ $u->email }})
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                Action
                <select name="action" onchange="this.form.submit()">
                    <option value="">All actions</option>
                    @foreach ($actionLabels as $slug => $label)
                        <option value="{{ $slug }}" @selected($actionFilter === $slug)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                Search
                <input type="search" name="q" value="{{ $search }}" placeholder="email, description, IP">
            </label>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if ($userFilter || $actionFilter || $search)
                <a href="{{ route('dashboard.users.activities') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>

        @if ($activities->isEmpty())
            <p class="muted-block">No activity matches the current filters.</p>
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>Who</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td title="{{ $activity->created_at?->toDateTimeString() }}">
                                    {{ $activity->created_at?->format('Y-m-d H:i:s') }}
                                </td>
                                <td>
                                    @if ($activity->user)
                                        <strong>
                                            {{ $activity->user->name }}
                                            @if ($activity->user->isSystem()) <span class="badge badge-success">system</span>@endif
                                        </strong>
                                        <div class="muted small">{{ $activity->user->email }}</div>
                                    @elseif ($activity->email)
                                        <span class="muted">{{ $activity->email }}</span>
                                        <div class="muted small">(no account)</div>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="role-pill">{{ $activity->label() }}</span>
                                </td>
                                <td>{{ $activity->description }}</td>
                                <td class="mono small">{{ $activity->ip_address }}</td>
                                <td>
                                    @if (! empty($activity->context))
                                        <details class="activity-context">
                                            <summary>view</summary>
                                            <pre>{{ json_encode($activity->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                            @if ($activity->user_agent)
                                                <div class="muted small">UA: {{ $activity->user_agent }}</div>
                                            @endif
                                        </details>
                                    @elseif ($activity->user_agent)
                                        <span class="muted small" title="{{ $activity->user_agent }}">UA</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $activities->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
