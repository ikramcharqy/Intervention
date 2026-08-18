<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    /**
     * Authenticate technician / user and generate Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données invalides.', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Identifiants incorrects.', null, 401);
        }

        if (isset($user->is_active) && !$user->is_active) {
            return $this->errorResponse('Votre compte est désactivé.', null, 403);
        }

        $token = $user->createToken('mobile-technician-token')->plainTextToken;

        \Illuminate\Support\Facades\Log::info("Connexion réussie", ['user_id' => $user->id, 'ip' => $request->ip()]);

        return $this->successResponse([
            'token' => $token,
            'user'  => [
                'id'        => $user->id,
                'name'      => $user->name,
                'prenom'    => $user->prenom,
                'email'     => $user->email,
                'roles'     => $user->getRoleNames(),
            ],
        ], 'Connexion réussie.');
    }

    /**
     * Revoke current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info("Déconnexion de l'utilisateur", ['user_id' => $request->user()->id, 'ip' => $request->ip()]);
        
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Déconnexion réussie.');
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse([
            'id'        => $user->id,
            'name'      => $user->name,
            'prenom'    => $user->prenom,
            'email'     => $user->email,
            'telephone' => $user->telephone,
            'photo'     => $user->photo,
            'roles'     => $user->getRoleNames(),
        ], 'Profil utilisateur récupéré.');
    }
}
