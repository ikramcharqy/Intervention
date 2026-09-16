<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ClientService
{
    public function __construct(
        private DocumentService $documentService,
    ) {
    }

    /**
     * Enregistre un nouveau client avec ses informations optionnelles d'entreprise.
     */
    public function createClient(array $data): Client
{
    return DB::transaction(function () use ($data) {

        // Création du compte utilisateur
        $user = User::create([
            'name'       => $data['nom'],
            'prenom'     => $data['nom_contact'] ?? $data['nom'],
            'email'      => $data['email'],
            'telephone'  => $data['telephone'],
            'adresse'    => $data['adresse_facturation'] ?? '',
            'password'   => Hash::make('password123'),
            'is_active'  => true,
        ]);

        // Attribution du rôle Client
        $user->assignRole('Client');

        // Création du client
        $client = Client::create([
            ...$data,
            'user_id' => $user->id,
        ]);

        // Informations entreprise
        if ($client->type_client === 'Entreprise') {
            $client->clientEntreprise()->create([
                'ice'     => $data['ice'] ?? null,
                'if'      => $data['if'] ?? null,
                'rc'      => $data['rc'] ?? null,
                'patente' => $data['patente'] ?? null,
            ]);
        }

        // Identité du client Particulier (CIN chiffré + date de naissance)
        if ($client->type_client === 'Particulier') {
            $client->clientParticulier()->create([
                'numero_cin'     => $data['numero_cin'] ?? null,
                'date_naissance' => $data['date_naissance'] ?? null,
            ]);

            $this->attachIdentityScans($client, $data);
        }

        return $client;
    });
}

    /**
     * Met à jour un client existant et ses informations d'entreprise.
     */
    public function updateClient(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update($data);

            if ($client->type_client === 'Entreprise') {
                $client->clientEntreprise()->updateOrCreate(
                    ['client_id' => $client->id],
                    [
                        'ice'     => $data['ice'] ?? null,
                        'if'      => $data['if'] ?? null,
                        'rc'      => $data['rc'] ?? null,
                        'patente' => $data['patente'] ?? null,
                    ]
                );
            } else {
                $client->clientEntreprise()?->delete();
            }

            if ($client->type_client === 'Particulier') {
                $client->clientParticulier()->updateOrCreate(
                    ['client_id' => $client->id],
                    [
                        'numero_cin'     => $data['numero_cin'] ?? null,
                        'date_naissance' => $data['date_naissance'] ?? null,
                    ]
                );

                $this->attachIdentityScans($client, $data);
            } else {
                $client->clientParticulier()?->delete();
            }

            return $client;
        });
    }

    /**
     * Verse les scans CIN recto/verso (si fournis) dans le module Documents
     * existant, plutôt que de dupliquer un mécanisme de stockage de fichiers :
     * chaque scan devient une entrée "Mes Documents" (Type=Pièce d'identité)
     * liée à ce client.
     */
    private function attachIdentityScans(Client $client, array $data): void
    {
        foreach (['cin_recto', 'cin_verso'] as $field) {
            if (!empty($data[$field])) {
                $this->documentService->store(
                    ['type_document' => "Pièce d'identité", 'client_id' => $client->id],
                    $data[$field],
                    auth()->id(),
                );
            }
        }
    }

    /**
     * Désactive logiquement un client.
     */
    public function deactivate(Client $client): void
    {
        $client->update(['is_active' => false]);
    }

    /**
     * Réactive un client.
     */
    public function activate(Client $client): void
    {
        $client->update(['is_active' => true]);
    }

    /**
     * Réassigne un client à un autre commercial, en conservant la trace
     * du changement (ClientObserver, déclenché par l'update du client).
     */
    public function reassignerCommercial(Client $client, User $nouveauCommercial, ?string $commentaire): Client
    {
        if (! $nouveauCommercial->hasRole('Commercial')) {
            throw new \InvalidArgumentException("Cet utilisateur n'a pas le rôle Commercial.");
        }

        $client->reassignment_comment = $commentaire;
        $client->update(['commercial_id' => $nouveauCommercial->id]);

        return $client->fresh();
    }
}
