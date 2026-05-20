<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, remember: false)) {
            ActivityLogger::log(
                action: 'auth.login_failed',
                description: 'Wrong email or password.',
                email: $credentials['email'],
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            ActivityLogger::log(
                action: 'auth.login_blocked',
                user: $user,
                description: 'Login blocked: account is inactive.',
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account is inactive. Contact an administrator.']);
        }

        if (! $user->canAccess('dashboard.login')) {
            ActivityLogger::log(
                action: 'auth.login_blocked',
                user: $user,
                description: 'Login blocked: role cannot sign in to the dashboard.',
                context: ['role' => $user->role?->slug],
            );

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account type cannot sign in to the dashboard.']);
        }

        $request->session()->regenerate();

        ActivityLogger::log(
            action: 'auth.login',
            user: $user,
            description: 'Signed in to the dashboard.',
            context: ['role' => $user->role?->slug],
        );

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            ActivityLogger::log(
                action: 'auth.logout',
                user: $user,
                description: 'Signed out.',
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
