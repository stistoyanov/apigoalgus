<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Goalgus API') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/GoalgusLogoIcon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="dashboard-page">
    <header class="dashboard-header">
        <div class="container">
            <a href="{{ route('dashboard') }}" class="brand">
                <img src="{{ asset('images/GoalgusLogo.png') }}" alt="Goalgus" width="160" height="44">
            </a>
            <nav class="dashboard-nav">
                <button type="button" class="dashboard-menu-toggle" aria-expanded="false" aria-controls="dashboard-sidebar" aria-label="Open menu">
                    <span class="dashboard-menu-icon" aria-hidden="true"></span>
                    <span class="sr-only">Menu</span>
                </button>
                <a href="{{ url('/') }}">Home</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Log out</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="dashboard-shell" id="dashboard-shell">
        <aside class="dashboard-sidebar" id="dashboard-sidebar">
            <nav aria-label="Dashboard">
                @php $u = auth()->user(); @endphp
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') && ! request()->routeIs('dashboard.*') ? 'is-active' : '' }}">Overview</a>
                @if (\App\Support\Access::allowed($u, 'files.view'))
                    <a href="{{ route('dashboard.files') }}" class="{{ request()->routeIs('dashboard.files*') ? 'is-active' : '' }}">Files</a>
                @endif
                @if (\App\Support\Access::allowed($u, 'sites.view'))
                    <a href="{{ route('dashboard.sites') }}" class="{{ request()->routeIs('dashboard.sites*') ? 'is-active' : '' }}">Sites</a>
                @endif
                @if (\App\Support\Access::allowed($u, 'scheduler.view'))
                    <a href="{{ route('dashboard.scheduler') }}" class="{{ request()->routeIs('dashboard.scheduler*') ? 'is-active' : '' }}">Scheduler</a>
                @endif
                @if (\App\Support\Access::allowed($u, 'logs.view'))
                    <a href="{{ route('dashboard.logs') }}" class="{{ request()->routeIs('dashboard.logs*') ? 'is-active' : '' }}">Logs</a>
                @endif
                @if (\App\Support\Access::allowed($u, 'email_test'))
                    <a href="{{ route('dashboard.email') }}" class="{{ request()->routeIs('dashboard.email*') ? 'is-active' : '' }}">Email test</a>
                @endif
                @if (\App\Support\Access::allowed($u, 'users.view'))
                    <a href="{{ route('dashboard.users') }}" class="{{ request()->routeIs('dashboard.users*') ? 'is-active' : '' }}">Users</a>
                @endif
            </nav>
        </aside>

        <main class="dashboard-content dashboard-main">
            @if (session('status'))
                <div class="status-banner" role="status">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="status-banner error" role="alert">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/dashboard.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
