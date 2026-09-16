<?php

namespace App\Services;

use App\Models\Client;
use App\Models\TicketSupport;
use App\Models\User;

/**
 * Logique métier des tickets de support du portail Client. Ne construit
 * volontairement aucun traitement côté Commercial/Admin (assignation,
 * changement de statut par un agent, notifications internes) : cette passe
 * se limite à la création et à la consultation côté Client — le traitement
 * interne est un chantier séparé à valider (cf. rapport de livraison).
 */
class TicketSupportService
{
    public function creer(Client $client, User $auteur, array $data): TicketSupport
    {
        return TicketSupport::create([
            'reference' => $this->genererReference(),
            'client_id' => $client->id,
            'user_id' => $auteur->id,
            'commercial_id' => $client->commercial_id,
            'categorie' => $data['categorie'] ?? null,
            'objet' => $data['objet'],
            'message' => $data['message'],
            'piece_jointe' => $data['piece_jointe'] ?? null,
            'statut' => TicketSupport::STATUT_OUVERT,
        ]);
    }

    public function changerStatut(TicketSupport $ticket, string $statut): TicketSupport
    {
        $ticket->update(['statut' => $statut]);

        return $ticket;
    }

    /**
     * Génère une référence unique au format SUP-{ANNEE}-{numéro séquentiel sur
     * 3 chiffres}, même convention que DevisService/FactureService/DemandeInterventionService.
     */
    private function genererReference(): string
    {
        $prefix = 'SUP-' . now()->format('Y') . '-';

        $numero = 1;
        do {
            $reference = $prefix . str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            $numero++;
        } while (TicketSupport::where('reference', $reference)->exists());

        return $reference;
    }
}
