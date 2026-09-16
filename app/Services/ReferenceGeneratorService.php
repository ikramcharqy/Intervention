<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\DemandeIntervention;
use App\Models\Emplacement;
use App\Models\Intervention;

/**
 * Génération centralisée des références d'entités. Avant ce Service, chaque point de
 * création (Chantier, Intervention) générait sa propre référence localement, avec des
 * formats incompatibles selon l'endroit (ex: CHT-ABC-01 vs CHT001, INT-{date}-{hex} vs
 * INT-GPS-{rand}). Les références déjà existantes en base ne sont jamais modifiées par
 * ce Service — seules les nouvelles créations passent désormais par ici.
 */
class ReferenceGeneratorService
{
    /**
     * Chantier : CHT-[INITIALES_CLIENT]-[NUM_SÉQUENTIEL_PAR_CLIENT]. Convention choisie
     * d'après le format déjà majoritaire en démo (ex: CHT-FZ-01 pour "Fatima Zahra").
     */
    public function generateChantierReference(Client $client): string
    {
        $initiales = $this->initiales($client->nom);
        $sequence = Chantier::where('client_id', $client->id)->count() + 1;

        do {
            $reference = "CHT-{$initiales}-" . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Chantier::where('code_chantier', $reference)->exists());

        return $reference;
    }

    /**
     * Demande d'intervention : DEM-[ANNÉE]-[NUM_SÉQUENTIEL_GLOBAL]. Logique reprise telle
     * quelle de DemandeInterventionService::generateUniqueReference() (déjà correcte et
     * déjà unifiée entre les flux Commercial et Portail Client) — centralisée ici pour que
     * Chantier/Intervention suivent le même point d'entrée unique.
     */
    public function generateDemandeReference(): string
    {
        $prefix = 'DEM-' . now()->format('Y') . '-';
        $numero = 1;

        do {
            $reference = $prefix . str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            $numero++;
        } while (DemandeIntervention::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Intervention : INT-[INITIALES_CLIENT]-[NUM_SÉQUENTIEL_PAR_CLIENT]. $client peut être
     * null (ex: chantier introuvable) — un préfixe générique est alors utilisé plutôt que
     * de faire échouer la création.
     */
    public function generateInterventionReference(?Client $client): string
    {
        $initiales = $client ? $this->initiales($client->nom) : 'GEN';
        $sequence = $client
            ? Intervention::whereHas('chantier', fn ($q) => $q->where('client_id', $client->id))->count() + 1
            : Intervention::count() + 1;

        do {
            $reference = "INT-{$initiales}-" . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Intervention::where('code_intervention', $reference)->exists());

        return $reference;
    }

    /**
     * Emplacement : EMP-[INITIALES_CLIENT]-[NUM_SÉQUENTIEL_PAR_CHANTIER]. Remplace les deux
     * formats aléatoires précédents (EmplacementService::generateUniqueQrCode() en
     * "EMP-QR-{hex}", EmplacementSeeder en "EMP-{random}") — ce Service devient le seul
     * point de génération pour toute nouvelle création ou régénération de QR Code ;
     * les références déjà en base (y compris aléatoires) ne sont jamais retouchées.
     */
    public function generateEmplacementReference(Chantier $chantier): string
    {
        $initiales = $chantier->client ? $this->initiales($chantier->client->nom) : 'GEN';
        $sequence = Emplacement::where('chantier_id', $chantier->id)->count() + 1;

        do {
            $reference = "EMP-{$initiales}-" . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Emplacement::where('qr_code', $reference)->exists());

        return $reference;
    }

    /**
     * Client : CL[NUM_SÉQUENTIEL_GLOBAL] (3 chiffres), format déjà majoritaire en
     * démo (CL001, CL002) — remplace le format aléatoire "CL-{uniqid}" utilisé par
     * ProspectConversionService. Séquence globale (un seul client "top-level"),
     * contrairement aux entités qui en dépendent (Chantier/Intervention/Emplacement),
     * qui restent séquencées par client.
     */
    public function generateClientReference(): string
    {
        $sequence = Client::count() + 1;

        do {
            $reference = 'CL' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            $sequence++;
        } while (Client::where('code_client', $reference)->exists());

        return $reference;
    }

    /**
     * Initiales déterministes à partir du nom client : une lettre par mot (max 3), ou les
     * 3 premières lettres du nom si un seul mot (ex: "Fatima Zahra" → "FZ", "Société ABC" →
     * "SA", "TechCorp" → "TEC").
     */
    private function initiales(string $nom): string
    {
        $mots = preg_split('/\s+/', trim($nom)) ?: [];
        $mots = array_values(array_filter($mots, fn ($m) => $m !== ''));

        if (count($mots) > 1) {
            $init = collect($mots)->map(fn ($m) => mb_strtoupper(mb_substr($m, 0, 1)))->implode('');
            return mb_substr($init, 0, 3) ?: 'CLT';
        }

        return mb_strtoupper(mb_substr($mots[0] ?? 'CLT', 0, 3));
    }
}
