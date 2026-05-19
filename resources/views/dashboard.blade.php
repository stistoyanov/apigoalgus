<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name', 'Goalgus API') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/GoalgusLogoIcon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="dashboard-page">
    <header class="dashboard-header">
        <div class="container">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/GoalgusLogo.png') }}" alt="Goalgus" width="160" height="44">
            </a>
            <nav class="dashboard-nav">
                <a href="{{ url('/') }}">Home</a>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Log out</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="dashboard-card">
            <h1>Dashboard</h1>
            <p>You are signed in to the Goalgus API.</p>
            <p class="user-email">{{ $user->email }}</p>
        </div>
    </main>
</body>
</html>
