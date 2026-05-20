@extends('layouts.dashboard')

@section('title', 'Sites')

@section('content')
    <div class="panel">
        <div class="panel-head">
            <div>
                <h1>Sites</h1>
                <p class="lead">Manage external websites served from api.goalgus.bg.</p>
            </div>
        </div>

        @if ($sites->isEmpty())
            <p class="muted-block">No sites configured yet.</p>
        @else
            <div class="site-cards">
                @foreach ($sites as $site)
                    <a href="{{ route('dashboard.sites.show', $site) }}" class="site-card">
                        <strong>{{ $site->name }}</strong>
                        <span class="muted small">{{ $site->domain }}</span>
                        <span class="role-pill">{{ $site->is_active ? 'Active' : 'Inactive' }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
