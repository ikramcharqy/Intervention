<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClientService
{
    /**
     * Enregistre un nouveau client avec ses informations optionnelles d'entreprise.
     */
    public function createClient(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $client = Client::create($data);

            if ($client->type_client === 'Entreprise') {
                $client->clientEntreprise()->create([
                    'ice'     => $data['ice'] ?? null,
                    'if'      => $data['if'] ?? null,
                    'rc'      => $data['rc'] ?? null,
                    'patente' => $data['patente'] ?? null,
                ]);
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

            return $client;
        });
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
}
