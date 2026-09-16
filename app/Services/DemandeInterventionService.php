<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Devis;
use App\Models\DemandeIntervention;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DemandeInterventionService
{
    public function __construct(private ReferenceGeneratorService $referenceGenerator)
    {
    }

    /**
     * Crée une Demande d'Intervention à partir d'un devis Accepté, à la demande
     * explicite du commercial (validation manuelle, pas d'automatisme).
     *
     * @throws \RuntimeException si le devis n'est pas éligible (pas accepté, pas de
     *                           client rattaché, ou une demande existe déjà pour ce devis).
     */
    public function createFromDevis(Devis $devis): DemandeIntervention
    {
        if (! in_array($devis->statut, ['Accepté', 'Accepte', 'Validé'], true)) {
            throw new \RuntimeException("Seul un devis Accepté peut générer une demande d'intervention.");
        }

        if ($devis->demande_intervention_id) {
            throw new \RuntimeException("Ce devis a déjà généré une demande d'intervention.");
        }

        if (! $devis->client_id) {
            throw new \RuntimeException("Ce devis n'est pas encore rattaché à un client. Convertissez d'abord le prospect en client.");
        }

        return DB::transaction(function () use ($devis) {
            $devis->loadMissing('prospect.typesIntervention');

            $demande = DemandeIntervention::create([
                'reference' => $this->generateUniqueReference(),
                'commercial_id' => $devis->commercial_id,
                'client_id' => $devis->client_id,
                'type_intervention_id' => $devis->prospect?->typesIntervention->first()?->id,
                'priorite' => 'Normale',
                'statut' => 'En attente',
                'objet' => $this->buildObjet($devis),
                'description' => "Généré automatiquement depuis le devis accepté {$devis->reference} (validation manuelle par le commercial).",
            ]);

            $devis->update(['demande_intervention_id' => $demande->id]);

            return $demande;
        });
    }

    private function buildObjet(Devis $devis): string
    {
        $besoins = $devis->prospect?->typesIntervention->pluck('nom')->implode(', ');

        return $besoins
            ? "Suite à devis accepté {$devis->reference} : {$besoins}"
            : "Suite à devis accepté {$devis->reference}";
    }

    /**
     * Crée une Demande d'Intervention soumise par le Client depuis le portail.
     * Centralise ici (au lieu du Controller) : résolution du commercial rattaché,
     * référence unique (même séquence DEM-{ANNEE}-{NNN} que le canal Commercial —
     * les deux entrées partageaient auparavant deux formats de référence différents),
     * et encodage des champs optionnels sans colonne dédiée (créneau souhaité, contact
     * sur site) dans `description`, sur le même principe que les notes de qualification
     * déjà ajoutées par DemandeInterventionController::validerEtConvertir()/refuser().
     */
    public function createFromClientPortal(Client $client, array $data, array $photos = []): DemandeIntervention
    {
        $commercialId = $client->commercial_id
            ?? User::role('Commercial')->first()?->id
            ?? User::whereHas('roles', fn ($q) => $q->where('name', 'Commercial'))->first()?->id;

        $descriptionParts = [trim($data['description'])];
        if (!empty($data['creneau_souhaite'])) {
            $descriptionParts[] = "[Créneau souhaité] " . trim($data['creneau_souhaite']);
        }
        if (!empty($data['contact_sur_site'])) {
            $descriptionParts[] = "[Contact sur site] " . trim($data['contact_sur_site']);
        }

        return DemandeIntervention::create([
            'reference' => $this->generateUniqueReference(),
            'commercial_id' => $commercialId,
            'client_id' => $client->id,
            'chantier_id' => $data['chantier_id'],
            'type_intervention_id' => $data['type_intervention_id'],
            'priorite' => $data['priorite'],
            'statut' => DemandeIntervention::STATUT_EN_ATTENTE,
            'objet' => $data['objet'],
            'description' => trim(implode("\n\n", $descriptionParts)),
            'photos' => $photos ?: null,
        ]);
    }

    /**
     * Génère une référence unique au format DEM-{ANNEE}-{numéro séquentiel sur 3 chiffres}.
     * Délègue désormais à ReferenceGeneratorService (source unique partagée avec
     * Chantier/Intervention) — signature et nom de méthode inchangés pour les appelants
     * existants.
     */
    public function generateUniqueReference(): string
    {
        return $this->referenceGenerator->generateDemandeReference();
    }
}
