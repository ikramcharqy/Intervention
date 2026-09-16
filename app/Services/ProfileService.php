<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\DemandeDesactivationCompteNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Met à jour le profil de l'utilisateur connecté (informations personnelles
     * et professionnelles). La photo remplace l'ancienne (supprimée du disque).
     */
    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $data['photo']->store('profiles', 'public');
        } else {
            unset($data['photo']);
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * Un Commercial/Technicien ne peut pas supprimer son propre compte (réservé
     * à l'Admin/Super Admin depuis la gestion des utilisateurs) : il peut en
     * revanche demander sa désactivation, ce qui notifie les administrateurs.
     */
    public function requestDeactivation(User $user): void
    {
        $admins = User::role(['Super Admin', 'admin'])->get();

        foreach ($admins as $admin) {
            $admin->notify(new DemandeDesactivationCompteNotification($user));
        }
    }
}
