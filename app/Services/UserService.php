<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Enregistre un collaborateur.
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        if (isset($data['photo'])) {
            $data['photo'] = $data['photo']->store('photos', 'public');
        }

        $user = User::create($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Met à jour un collaborateur.
     */
    public function updateUser(User $user, array $data): User
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['photo'])) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $data['photo']->store('photos', 'public');
        }

        $user->update($data);

        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user;
    }

    /**
     * Désactive logiquement un collaborateur.
     */
    public function deactivate(User $user): void
    {
        $user->update(['is_active' => false]);
    }

    /**
     * Réactive un collaborateur.
     */
    public function activate(User $user): void
    {
        $user->update(['is_active' => true]);
    }
}
