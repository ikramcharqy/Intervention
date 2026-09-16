<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService,
    ) {
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return $this->roleView('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->profileService->updateProfile($request->user(), $request->validated());

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account (réservé Admin/Super Admin — voir requestDeactivation
     * pour le flux Commercial/Technicien).
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasAnyRole(['Super Admin', 'admin']), 403);

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Demande de désactivation de compte (Commercial/Technicien) : notifie les
     * administrateurs au lieu d'une auto-suppression en self-service.
     */
    public function requestDeactivation(Request $request): RedirectResponse
    {
        $this->profileService->requestDeactivation($request->user());

        return Redirect::route('profile.edit')->with('status', 'deactivation-requested');
    }
}
