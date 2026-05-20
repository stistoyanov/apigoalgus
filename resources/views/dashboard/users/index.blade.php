@extends('layouts.dashboard')

@section('title', 'Users')

@php
    $openCreate = $errors->any() && old('email');
@endphp

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>Users</h1>
                <p class="lead">Manage administrator accounts. Each user is assigned exactly one role.</p>
            </div>
            <button type="button" class="btn btn-primary" data-modal-open="user-create">
                + Create user
            </button>
        </div>

        @include('dashboard.users._tabs')

        <form class="toolbar" method="GET" action="{{ route('dashboard.users') }}">
            <label>
                Search by email
                <input type="search" name="q" value="{{ $search }}" placeholder="user@example.com">
            </label>
            <label>
                Role
                <select name="role" onchange="this.form.submit()">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->slug }}" @selected($roleFilter === $role->slug)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" class="btn btn-primary">Filter</button>
            @if ($search || $roleFilter)
                <a href="{{ route('dashboard.users') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>

        @if ($users->isEmpty())
            <p class="muted-block">No users match the current filters.</p>
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    {{ $user->name }}
                                    @if ($user->isMaster()) <span class="badge badge-success">master</span>@endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->role)
                                        <span class="role-pill">{{ $user->role->name }}</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('dashboard.users.toggle', $user) }}" class="inline-form">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                                        <input type="hidden" name="q" value="{{ $search }}">
                                        <input type="hidden" name="page" value="{{ $users->currentPage() }}">
                                        <button type="submit"
                                                class="switch {{ $user->is_active ? 'is-on' : '' }}"
                                                role="switch"
                                                aria-checked="{{ $user->is_active ? 'true' : 'false' }}"
                                                aria-label="Toggle active state for {{ $user->email }}"
                                                @disabled($user->isMaster())>
                                            <span class="switch-track"><span class="switch-knob"></span></span>
                                            <span class="switch-label">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                                <td class="col-actions">
                                    <button type="button"
                                            class="btn btn-outline btn-sm"
                                            data-modal-open="user-edit"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-role="{{ $user->role_id }}"
                                            data-user-active="{{ $user->is_active ? '1' : '0' }}"
                                            data-user-master="{{ $user->isMaster() ? '1' : '0' }}"
                                            data-action="{{ route('dashboard.users.update', $user) }}">
                                        Edit
                                    </button>
                                    @unless ($user->isMaster())
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                data-modal-open="user-delete"
                                                data-user-email="{{ $user->email }}"
                                                data-action="{{ route('dashboard.users.destroy', $user) }}">
                                            Delete
                                        </button>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links('vendor.pagination.default') }}
        @endif
    </div>

    @include('dashboard.users._modals', ['roles' => $roles, 'openCreate' => $openCreate])
@endsection

@push('scripts')
    <script src="{{ asset('js/auth.js') }}" defer></script>
@endpush
