<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Regroupement des types de App\Notifications\ClientPortalNotification pour le
     * filtre par catégorie, cohérent avec le pattern déjà utilisé sur Mes
     * Interventions (onglets) et Documents & Contrats (catégories).
     */
    private const GROUPES = [
        'interventions' => ['intervention_planifiee', 'intervention_demarree', 'intervention_terminee'],
        'rapports' => ['rapport_disponible'],
        'demandes' => ['demande_soumise', 'demande_convertie', 'demande_refusee'],
    ];

    public function index(Request $request): View
    {
        $user = auth()->user();
        $typeFiltre = $request->input('type', 'tous');

        $query = $user->notifications();

        if ($typeFiltre !== 'tous' && isset(self::GROUPES[$typeFiltre])) {
            $query->whereIn('data->type', self::GROUPES[$typeFiltre]);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('client.notifications.index', compact('notifications', 'typeFiltre', 'unreadCount'));
    }

    /**
     * Marque la notification comme lue puis redirige vers la ressource concernée
     * (fiche intervention, aperçu de rapport, liste des demandes...).
     */
    public function open(string $id): RedirectResponse
    {
        $user = auth()->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        return redirect()->route($data['lien_route'] ?? 'client.notifications.index', $data['lien_params'] ?? []);
    }

    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
