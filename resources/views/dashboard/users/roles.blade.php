@extends('layouts.dashboard')

@section('title', 'Roles')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>Roles</h1>
                <p class="lead">Read-only overview of available roles and the users assigned to each.</p>
            </div>
        </div>

        @include('dashboard.users._tabs')

        <div class="role-cards">
            @foreach ($roles as $role)
                <details class="role-card">
                    <summary>
                        <div class="role-card-head">
                            <div>
                                <h2>{{ $role->name }}</h2>
                                <p class="muted"><code>{{ $role->slug }}</code></p>
                            </div>
                            <div class="role-card-count">
                                <span class="count-number">{{ $role->users_count }}</span>
                                <span class="count-label">{{ \Illuminate\Support\Str::plural('user', $role->users_count) }}</span>
                            </div>
                        </div>
                        <p class="role-card-desc">{{ $role->description }}</p>
                        <span class="role-card-toggle">Show users</span>
                    </summary>
                    <div class="role-card-body">
                        @if ($role->users->isEmpty())
                            <p class="muted-block">No users assigned to this role.</p>
                        @else
                            <ul class="role-user-list">
                                @foreach ($role->users as $user)
                                    <li>
                                        <strong>{{ $user->name }}</strong>
                                        <span class="muted">&lt;{{ $user->email }}&gt;</span>
                                        @if (! $user->is_active)
                                            <span class="badge badge-failed">inactive</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </div>
@endsection
