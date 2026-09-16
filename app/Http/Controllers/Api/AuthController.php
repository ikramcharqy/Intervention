<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\ApiSessionService;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    /**
     * Authenticate technician / user and generate Sanctum token.
     */
    public function login(Request $request, TwoFactorAuthService $twoFactor, ApiSessionService $apiSession): JsonResponse
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

        // Étape 2 du prompt "Vérification du contournement 2FA" : cette API (jetons
        // Sanctum pour l'app mobile Technicien) ne peut pas faire respecter la 2FA
        // obligatoire des rôles à privilège élevé — leur authentification par ce canal
        // est donc refusée dans tous les cas, qu'ils aient ou non activé leur 2FA.
        if ($twoFactor->accountRequiresMandatory2FA($user)) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => "Tentative de connexion API refusée pour {$user->email} — rôle à privilège élevé, doit utiliser l'interface web avec 2FA",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'WARNING',
                'ip_address' => $request->ip(),
            ]);

            return $this->errorResponse(
                "Ce compte doit s'authentifier via l'interface web (authentification à deux facteurs obligatoire pour ce rôle).",
                null,
                403
            );
        }

        $token = $user->createToken('mobile-technician-token')->plainTextToken;

        // L'événement Illuminate\Auth\Events\Login (qui alimente LoginHistory
        // côté web, cf. AppServiceProvider) ne se déclenche jamais ici —
        // authentification manuelle par jeton, pas Auth::attempt().
        $apiSession->enregistrerConnexion($user, $request);

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
