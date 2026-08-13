<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user) {
            if ($user->hasRole('Super Admin')) {
                return redirect()->intended(route('superadmin.dashboard', absolute: false));
            }
            if ($user->hasRole('Commercial')) {
                return redirect()->intended(route('commercial.dashboard', absolute: false));
            }
            if ($user->hasRole('Client')) {
                return redirect()->intended(route('client.dashboard', absolute: false));
            }
            if ($user->hasRole('technicien') || $user->hasRole('Technicien')) {
                return redirect()->intended(route('technicien.dashboard', absolute: false));
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
