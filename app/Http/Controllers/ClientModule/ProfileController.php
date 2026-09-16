<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LoginHistory;
use App\Services\SessionSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function __construct(
        private SessionSecurityService $sessionSecurityService,
    ) {
    }

    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    public function edit(): View
    {
        $user = auth()->user();
        $client = $this->getClient();
        $client?->load('clientEntreprise');

        return view('client.profile', compact('user', 'client'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'telephone' => ['nullable', 'string', 'max:20'],
            'adresse' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return redirect()->route('client.profile.edit')->with('success', 'Vos informations personnelles ont été mises à jour avec succès.');
    }

    /**
     * Page Sécurité : dernière connexion, sessions actives (table `sessions`,
     * driver déjà en `database`), historique de connexion (LoginHistory) et
     * changement de mot de passe.
     */
    public function securite(Request $request): View
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        return view('client.securite.index', [
            'derniereConnexion' => $user->last_login_at ?? null,
            'ip' => $request->ip(),
            'sessions' => $this->sessionSecurityService->sessionsActives($user, $sessionCouranteId),
            'historique' => LoginHistory::where('user_id', $user->id)
                ->latest('logged_in_at')
                ->limit(10)
                ->get(),
        ]);
    }

    public function revoquerSession(Request $request, string $id): RedirectResponse
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        if ($id === $sessionCouranteId) {
            return back()->with('error', 'Impossible de déconnecter votre session actuelle depuis cette liste — utilisez le bouton Déconnexion.');
        }

        $this->sessionSecurityService->revoquerSession($user, $id);

        return back()->with('success', 'Session déconnectée avec succès.');
    }

    public function revoquerAutresSessions(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $sessionCouranteId = $request->session()->getId();

        $nb = $this->sessionSecurityService->revoquerAutresSessions($user, $sessionCouranteId);

        return back()->with('success', $nb > 0
            ? "Déconnecté(e) de {$nb} autre(s) session(s)."
            : 'Aucune autre session active à déconnecter.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Cohérent avec la gestion de sessions de l'étape 2 : un changement de mot
        // de passe déconnecte par sécurité tous les autres appareils.
        $nb = $this->sessionSecurityService->revoquerAutresSessions($user, $request->session()->getId());

        $message = 'Votre mot de passe a été modifié avec succès.';
        if ($nb > 0) {
            $message .= ' Vos autres sessions ont été déconnectées par sécurité.';
        }

        return redirect()->route('client.securite.index')->with('success', $message);
    }
}
