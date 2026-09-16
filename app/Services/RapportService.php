<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\Rapport;
use Illuminate\Support\Facades\DB;

class RapportService
{
    /**
     * Enregistre un rapport d'intervention.
     * Le statut de l'intervention n'est pas modifié ici — il est géré
     * exclusivement par InterventionService (workflow centralisé).
     */
    public function createRapport(array $data): Rapport
    {
        return Rapport::create($data);
    }

    /**
     * Met à jour le rapport d'intervention.
     */
    public function updateRapport(Rapport $rapport, array $data): Rapport
    {
        $rapport->update($data);
        return $rapport;
    }

    /**
     * Supprime le rapport et replace l'intervention en cours.
     */
    public function deleteRapport(Rapport $rapport): void
    {
        DB::transaction(function () use ($rapport) {
            // Remet l'intervention en cours
            $rapport->intervention->update([
                'statut' => 'En cours',
                'date_reelle_fin' => null,
            ]);

            $rapport->delete();
        });
    }

    /**
     * Vérifie si le client a le droit de consulter le rapport.
     * Terminee = le technicien a terminé sur le terrain ; Validee = l'admin a
     * ensuite validé et clôturé définitivement (statut postérieur à Terminee,
     * cf. InterventionService::validateIntervention()). Les deux donnent accès.
     */
    public function canClientView(Rapport $rapport, $user): bool
    {
        if ($user->hasRole('Client')) {
            return in_array($rapport->intervention->statut, [
                Intervention::STATUT_TERMINEE,
                Intervention::STATUT_VALIDEE,
            ]);
        }

        return true; // Les admins, commerciaux et techniciens peuvent toujours le voir
    }

    /**
     * Statistiques globales de certification des rapports, pour la barre KPI
     * de la liste des rapports. Un rapport est "certifié" si l'intervention
     * est Terminee ou Validee (les deux donnent un rapport définitif).
     */
    public function statsCertification(): array
    {
        $total = Rapport::count();
        $certifies = Rapport::whereHas('intervention', fn ($q) => $q->whereIn('statut', [
            Intervention::STATUT_TERMINEE,
            Intervention::STATUT_VALIDEE,
        ]))->count();

        return [
            'total'      => $total,
            'certifies'  => $certifies,
            'enRevision' => $total - $certifies,
            'taux'       => $total > 0 ? round(($certifies / $total) * 100) : 0,
        ];
    }

    /**
     * Calcule l'état d'affichage (libellé, thème couleur, icône) du statut de
     * validation d'un rapport — source de vérité unique, réutilisée par la
     * liste des rapports et la fiche de détail pour éviter toute divergence.
     */
    public function statutBadge(Rapport $rapport): array
    {
        $statut = $rapport->intervention->statut ?? null;

        return match ($statut) {
            Intervention::STATUT_VALIDEE => [
                'label'       => 'Validé',
                'description' => "Le rapport a été validé et clôturé définitivement par l'administrateur.",
                'theme'       => 'success',
                'icon'        => 'ti-circle-check',
            ],
            Intervention::STATUT_TERMINEE => [
                'label'       => 'Terminé — en attente de validation',
                'description' => "L'intervention est terminée sur le terrain. Le rapport doit encore être validé par l'administrateur.",
                'theme'       => 'warning',
                'icon'        => 'ti-clock',
            ],
            Intervention::STATUT_REJETEE, Intervention::STATUT_ANNULEE => [
                'label'       => 'Bloqué',
                'description' => "Le statut actuel de l'intervention est : {$statut}.",
                'theme'       => 'danger',
                'icon'        => 'ti-alert-circle',
            ],
            default => [
                'label'       => 'En cours de traitement',
                'description' => "Le statut actuel de l'intervention est : " . ($statut ?? 'N/A') . '.',
                'theme'       => 'warning',
                'icon'        => 'ti-clock',
            ],
        };
    }
}
