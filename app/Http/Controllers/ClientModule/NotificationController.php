<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Intervention;
use App\Models\Rapport;
use Illuminate\View\View;

class NotificationController extends Controller
{
    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    public function index(): View
    {
        $client = $this->getClient();

        if (!$client) {
            $notifications = collect();
            return view('client.notifications.index', compact('notifications'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        // Récupérer les événements d'intervention du client
        $interventions = Intervention::with(['chantier', 'typeIntervention', 'technicien', 'rapport'])
            ->whereIn('chantier_id', $chantierIds)
            ->latest('updated_at')
            ->limit(30)
            ->get();

        $notifications = collect();

        foreach ($interventions as $interv) {
            // Planifiée
            if ($interv->statut === Intervention::STATUT_PLANIFIEE) {
                $notifications->push([
                    'type' => 'planifiee',
                    'title' => "Intervention planifiée : {$interv->code_intervention}",
                    'message' => "L'intervention sur le chantier \"{$interv->chantier->nom}\" a été planifiée pour le {$interv->date_prevue_debut?->format('d/m/Y H:i')}.",
                    'date' => $interv->updated_at,
                    'badge' => 'kt-badge-primary',
                    'link' => route('client.interventions.show', $interv),
                ]);
            }

            // Commencée (En cours)
            if ($interv->statut === Intervention::STATUT_EN_COURS) {
                $notifications->push([
                    'type' => 'commencee',
                    'title' => "Intervention démarrée : {$interv->code_intervention}",
                    'message' => "Le technicien {$interv->technicien?->name} a démarré l'intervention sur le chantier \"{$interv->chantier->nom}\".",
                    'date' => $interv->updated_at,
                    'badge' => 'kt-badge-warning',
                    'link' => route('client.interventions.show', $interv),
                ]);
            }

            // Terminée
            if ($interv->statut === Intervention::STATUT_TERMINEE) {
                $notifications->push([
                    'type' => 'terminee',
                    'title' => "Intervention terminée : {$interv->code_intervention}",
                    'message' => "L'intervention sur le chantier \"{$interv->chantier->nom}\" a été clôturée avec succès.",
                    'date' => $interv->updated_at,
                    'badge' => 'kt-badge-success',
                    'link' => route('client.interventions.show', $interv),
                ]);
            }

            // Rapport disponible
            if ($interv->rapport) {
                $notifications->push([
                    'type' => 'rapport',
                    'title' => "Rapport disponible : {$interv->code_intervention}",
                    'message' => "Le rapport de l'intervention sur \"{$interv->chantier->nom}\" est maintenant disponible au téléchargement.",
                    'date' => $interv->rapport->created_at,
                    'badge' => 'kt-badge-info',
                    'link' => route('client.rapports.show', $interv->rapport),
                ]);
            }
        }

        // Trier les notifications de la plus récente à la plus ancienne
        $notifications = $notifications->sortByDesc('date')->values();

        return view('client.notifications.index', compact('notifications'));
    }
}
