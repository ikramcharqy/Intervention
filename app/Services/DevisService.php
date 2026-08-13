<?php

namespace App\Services;

use App\Models\Devis;
use Illuminate\Support\Facades\DB;

class DevisService
{
    public function create(array $data): Devis
    {
        return DB::transaction(function () use ($data) {

            $devis = Devis::create([
                'numero' => $this->generateNumero(),
                'prospect_id' => $data['prospect_id'] ?? null,
                'client_id' => $data['client_id'] ?? null,
                'commercial_id' => auth()->id(),
                'objet' => $data['objet'],
                'description' => $data['description'] ?? null,
                'remise' => $data['remise'] ?? 0,
                'tva' => $data['tva'] ?? 20,
                'date_emission' => now(),
                'date_expiration' => $data['date_expiration'],
                'statut' => 'Brouillon',
                'total_ht' => 0,
                'total_ttc' => 0,
            ]);

            $total = 0;

            foreach ($data['lignes'] as $ligne) {

                $montant = $ligne['quantite'] * $ligne['prix_unitaire'];

                $devis->lignes()->create([
                    'designation' => $ligne['designation'],
                    'description' => $ligne['description'] ?? null,
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'montant_ht' => $montant,
                ]);

                $total += $montant;
            }

            $total -= $devis->remise;

            $ttc = $total + ($total * ($devis->tva / 100));

            $devis->update([
                'total_ht' => $total,
                'total_ttc' => $ttc,
            ]);

            return $devis;
        });
    }

    private function generateNumero(): string
    {
        return 'DEV-' . now()->format('Ymd') . '-' . rand(1000, 9999);
    }
}