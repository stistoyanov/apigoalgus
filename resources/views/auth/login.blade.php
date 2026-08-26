@extends('layouts.auth')

@section('title', 'Login')

@section('content')
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
                <div class="input-with-action">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >
                    <button
                        type="button"
                        class="input-toggle"
                        data-password-toggle="password"
                        aria-label="Show password"
                        aria-pressed="false"
                    >
                        @include('partials.eye-icons')
                    </button>
                </div>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit btn-block">Sign in</button>
        </form>
    </div>
@endsection
