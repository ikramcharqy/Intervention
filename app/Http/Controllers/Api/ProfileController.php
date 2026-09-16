<?php

namespace App\Http\Controllers\Api;

use App\Models\Intervention;
use App\Services\ApiSessionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends BaseApiController
{
    public function __construct(private ApiSessionService $apiSession)
    {
    }

    /**
     * Obtenir le profil complet du technicien connecté avec ses statistiques.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        // Statistiques d'interventions pour le technicien
        $stats = [
            'total_interventions'     => Intervention::where('technicien_id', $user->id)->count(),
            'interventions_en_cours'  => Intervention::where('technicien_id', $user->id)->where('statut', Intervention::STATUT_EN_COURS)->count(),
            'interventions_terminees' => Intervention::where('technicien_id', $user->id)->where('statut', Intervention::STATUT_TERMINEE)->count(),
            'interventions_en_attente'=> Intervention::where('technicien_id', $user->id)->where('statut', Intervention::STATUT_FORM_REMPLI)->count(),
            'unread_notifications'    => $user->unreadNotifications()->count(),
        ];

        $photoUrl = $user->photo ? asset('storage/' . $user->photo) : null;

        return $this->successResponse([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'prenom'    => $user->prenom,
                'email'     => $user->email,
                'telephone' => $user->telephone,
                'adresse'   => $user->adresse,
                'photo'     => $user->photo,
                'photo_url' => $photoUrl,
                'is_active' => (bool) $user->is_active,
                'notification_preferences' => $user->notification_preferences,
                'roles'     => $user->getRoleNames(),
            ],
            'statistics' => $stats,
        ], 'Profil technicien récupéré avec succès.');
    }

    /**
     * Mettre à jour les informations personnelles du profil.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string|max:255',
        ], [
            'name.required'   => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required'  => 'L\'adresse email est obligatoire.',
            'email.email'     => 'L\'adresse email doit être valide.',
            'email.unique'    => 'Cette adresse email est déjà utilisée.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données invalides.', $validator->errors(), 422);
        }

        try {
            $user->update($validator->validated());

            $photoUrl = $user->photo ? asset('storage/' . $user->photo) : null;

            return $this->successResponse([
                'id'        => $user->id,
                'name'      => $user->name,
                'prenom'    => $user->prenom,
                'email'     => $user->email,
                'telephone' => $user->telephone,
                'adresse'   => $user->adresse,
                'photo'     => $user->photo,
                'photo_url' => $photoUrl,
                'roles'     => $user->getRoleNames(),
            ], 'Profil mis à jour avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Mettre à jour la photo de profil du technicien.
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'photo.required' => 'Une image doit être fournie.',
            'photo.image'    => 'Le fichier doit être une image.',
            'photo.mimes'    => 'L\'image doit être au format jpeg, png, jpg ou webp.',
            'photo.max'      => 'La taille de l\'image ne peut pas dépasser 5 Mo.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Photo invalide.', $validator->errors(), 422);
        }

        try {
            // Suppression de l'ancienne photo si elle existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Stockage de la nouvelle photo
            $path = $request->file('photo')->store('profiles', 'public');
            $user->update(['photo' => $path]);

            return $this->successResponse([
                'photo'     => $path,
                'photo_url' => asset('storage/' . $path),
            ], 'Photo de profil mise à jour avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Mettre à jour le statut de présence "En service"/"Hors service"
     * déclaré par le technicien (PresenceToggle côté app mobile). Ne
     * déclenche aucun tracking GPS — question de conformité non tranchée,
     * cf. commentaires PresenceService.php côté Flutter.
     */
    public function updatePresence(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'en_service' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données invalides.', $validator->errors(), 422);
        }

        $user = $request->user();
        $user->update([
            'en_service' => $request->boolean('en_service'),
            'en_service_maj_le' => now(),
        ]);

        return $this->successResponse([
            'en_service' => $user->en_service,
            'en_service_maj_le' => $user->en_service_maj_le,
        ], 'Statut de présence mis à jour.');
    }

    /**
     * Changer le mot de passe de l'utilisateur.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required'         => 'Le nouveau mot de passe est obligatoire.',
            'password.min'              => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'        => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation du mot de passe échouée.', $validator->errors(), 422);
        }

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return $this->errorResponse('Le mot de passe actuel saisi est incorrect.', null, 400);
        }

        try {
            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            return $this->successResponse(null, 'Mot de passe modifié avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Préférences de notification par catégorie — actuellement une seule
     * catégorie réellement exploitée (`intervention_updates`, cf.
     * InterventionService::souhaiteNotification()) ; conçu en tableau
     * `categorie => bool` pour rester extensible sans nouvelle migration si
     * d'autres catégories sont ajoutées plus tard.
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'intervention_updates' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données invalides.', $validator->errors(), 422);
        }

        $user = $request->user();
        $user->update([
            'notification_preferences' => array_merge(
                $user->notification_preferences ?? [],
                ['intervention_updates' => $request->boolean('intervention_updates')]
            ),
        ]);

        return $this->successResponse(
            ['notification_preferences' => $user->notification_preferences],
            'Préférences de notification mises à jour.'
        );
    }

    /**
     * Historique des connexions API (jetons Sanctum) — distinct de
     * l'historique de connexion web (SessionSecurityService), qui ne
     * concerne que les sessions par cookie.
     */
    public function loginHistory(Request $request): JsonResponse
    {
        return $this->successResponse(
            $this->apiSession->historiqueConnexions($request->user()),
            'Historique de connexion récupéré.'
        );
    }

    /**
     * Appareils connectés (un jeton Sanctum = une connexion API distincte).
     */
    public function sessions(Request $request): JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()?->id;

        return $this->successResponse(
            $this->apiSession->appareilsActifs($request->user(), $currentTokenId),
            'Appareils connectés récupérés.'
        );
    }

    /**
     * Révoque un appareil (jeton Sanctum) précis — y compris potentiellement
     * l'appareil courant, ce qui déconnectera immédiatement l'app appelante ;
     * comportement attendu d'une action "révoquer cet appareil".
     */
    public function revokeSession(Request $request, int $tokenId): JsonResponse
    {
        $revoked = $this->apiSession->revoquerAppareil($request->user(), $tokenId);

        if (!$revoked) {
            return $this->errorResponse('Appareil introuvable.', null, 404);
        }

        return $this->successResponse(null, 'Appareil déconnecté avec succès.');
    }
}
