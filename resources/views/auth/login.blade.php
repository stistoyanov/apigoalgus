<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — {{ config('app.name', 'Goalgus API') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/GoalgusLogoIcon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <header class="auth-header">
        <div class="container">
            <a href="{{ url('/') }}" class="brand">
                <img src="{{ asset('images/GoalgusLogo.png') }}" alt="Goalgus" width="160" height="44">
            </a>
            <a href="{{ url('/') }}" class="back-link">&larr; Back to home</a>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <h1>Sign in</h1>
            <p class="subtitle">Goalgus API administration</p>

            @if ($errors->any())
                <div class="auth-alert" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="auth-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Sign in</button>
            </form>
        </div>
    </main>
</body>
</html>
