<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Enregistre ou met à jour l'abonnement Web Push du navigateur/téléphone du technicien.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint'    => 'required|string',
            'public_key'  => 'nullable|string',
            'auth_token'  => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);

        $user = Auth::user();

        $sub = PushSubscription::updateOrCreate(
            [
                'user_id'  => $user->id,
                'endpoint' => $request->endpoint,
            ],
            [
                'public_key'  => $request->public_key,
                'auth_token'  => $request->auth_token,
                'device_name' => $request->device_name ?: substr($request->userAgent() ?? 'Mobile', 0, 191),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Abonnement Push enregistré avec succès.',
            'data'    => $sub,
        ]);
    }

    /**
     * Supprime l'abonnement Push (désabonnement ou déconnexion).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Abonnement Push supprimé.',
        ]);
    }

    /**
     * Retourne le statut d'abonnement de l'utilisateur courant.
     */
    public function status(Request $request): JsonResponse
    {
        $count = PushSubscription::where('user_id', Auth::id())->count();

        return response()->json([
            'success'   => true,
            'subscribed' => $count > 0,
            'count'     => $count,
        ]);
    }
}
