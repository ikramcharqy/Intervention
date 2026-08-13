<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Liste de toutes les notifications du technicien connecté.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->paginate($request->input('per_page', 15));

        $unreadCount = $user->unreadNotifications()->count();

        return $this->successResponse([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications,
        ], 'Liste des notifications récupérée avec succès.');
    }

    /**
     * Obtenir uniquement les notifications non lues.
     */
    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadNotifications = $user->unreadNotifications()->get();

        return $this->successResponse([
            'unread_count'  => $unreadNotifications->count(),
            'notifications' => $unreadNotifications,
        ], 'Notifications non lues récupérées avec succès.');
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->first();

        if (!$notification) {
            return $this->errorResponse('Notification non trouvée.', null, 404);
        }

        $notification->markAsRead();

        return $this->successResponse(null, 'Notification marquée comme lue.');
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->unreadNotifications->markAsRead();

        return $this->successResponse(null, 'Toutes les notifications ont été marquées comme lues.');
    }
}
