<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Intervention;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use Illuminate\Database\Seeder;

/**
 * Notifications de démonstration pour le portail Client (Société ABC), avec des
 * horodatages réellement étalés — contrairement à l'ancien flux qui recalculait
 * des notifications à la volée à partir de `updated_at` sur les interventions,
 * or plusieurs d'entre elles (créées dans la même exécution de ClientSeeder)
 * partageaient un timestamp identique à la seconde près, ce qui n'était pas
 * réaliste. Ici, chaque notification est un enregistrement réel de la table
 * `notifications` (même mécanisme que le Technicien/l'API), avec son propre
 * created_at explicite.
 *
 * Idempotent au niveau "n'exécute qu'une fois" : si l'utilisateur a déjà des
 * notifications, ne rejoue rien (les notifications n'ont pas de clé naturelle
 * permettant un updateOrCreate propre).
 */
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $client = Client::where('code_client', 'CL001')->first();
        $user = $client?->utilisateurPortail();

        if (!$user || $user->notifications()->exists()) {
            return;
        }

        $intervTerminee = Intervention::where('code_intervention', 'INT-ABC-001')->first();
        $intervEnCours = Intervention::where('code_intervention', 'INT-ABC-002')->first();
        $intervPlanifiee = Intervention::where('code_intervention', 'INT-ABC-003')->first();

        if ($intervPlanifiee) {
            $this->creerNotification(
                $user,
                'intervention_planifiee',
                'Intervention planifiée',
                "L'intervention {$intervPlanifiee->code_intervention} sur \"{$intervPlanifiee->chantier?->nom}\" a été planifiée.",
                'client.interventions.show',
                [$intervPlanifiee->id],
                now()->subDays(3)->setTime(9, 12),
                lu: true,
            );
        }

        if ($intervEnCours) {
            $this->creerNotification(
                $user,
                'intervention_demarree',
                'Intervention démarrée',
                "Le technicien {$intervEnCours->technicien?->name} a démarré l'intervention {$intervEnCours->code_intervention}.",
                'client.interventions.show',
                [$intervEnCours->id],
                now()->subHours(26)->subMinutes(15),
                lu: true,
            );
        }

        if ($intervTerminee) {
            $this->creerNotification(
                $user,
                'intervention_terminee',
                'Intervention terminée',
                "L'intervention {$intervTerminee->code_intervention} sur \"{$intervTerminee->chantier?->nom}\" a été clôturée.",
                'client.interventions.show',
                [$intervTerminee->id],
                now()->subHours(5)->subMinutes(40),
                lu: false,
            );

            if ($intervTerminee->rapport) {
                $this->creerNotification(
                    $user,
                    'rapport_disponible',
                    'Rapport disponible',
                    "Le rapport de l'intervention {$intervTerminee->code_intervention} est maintenant disponible.",
                    'client.rapports.show',
                    [$intervTerminee->rapport->id],
                    now()->subHours(4)->subMinutes(52),
                    lu: false,
                );
            }
        }
    }

    private function creerNotification(User $user, string $type, string $titre, string $message, string $route, array $params, \Carbon\Carbon $date, bool $lu): void
    {
        $user->notify(new ClientPortalNotification($type, $titre, $message, $route, $params));

        $notification = $user->notifications()->latest()->first();
        $notification->forceFill([
            'created_at' => $date,
            'updated_at' => $date,
            'read_at' => $lu ? $date->copy()->addMinutes(5) : null,
        ])->save();
    }
}
