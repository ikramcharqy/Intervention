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

        // Redirection directe par rôle (pas de redirect()->intended()) : une URL "intended"
        // mémorisée en session peut appartenir à un espace réservé à un autre rôle (ex.
        // /superadmin/dashboard visité sans être connecté), ce qui renvoyait l'utilisateur
        // vers une page qu'il n'a pas le droit de voir (403) au lieu de son propre dashboard.
        $request->session()->forget('url.intended');

        if ($user) {
            if ($user->hasRole('Super Admin')) {
                return redirect()->route('superadmin.dashboard');
            }
            if ($user->hasRole('Commercial')) {
                return redirect()->route('commercial.dashboard');
            }
            if ($user->hasRole('Client')) {
                return redirect()->route('client.dashboard');
            }
            if ($user->hasRole('technicien') || $user->hasRole('Technicien')) {
                return redirect()->route('technicien.dashboard');
            }
        }

        return redirect()->route('dashboard');
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
