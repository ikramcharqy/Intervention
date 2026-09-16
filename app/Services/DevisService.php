<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DevisService
{
    public function __construct(
        private DocumentService $documentService,
    ) {
    }

    public function create(array $data): Devis
    {
        return DB::transaction(function () use ($data) {
            $data['reference'] = $this->generateUniqueReference();
            $data['commercial_id'] = auth()->id();

            $devis = Devis::create($this->withMontants($data, $data['lignes']));

            $this->syncLignes($devis, $data['lignes']);

            return $devis;
        });
    }

    public function update(Devis $devis, array $data): Devis
    {
        return DB::transaction(function () use ($devis, $data) {
            $devis->update($this->withMontants($data, $data['lignes']));

            $devis->lignes()->delete();
            $this->syncLignes($devis, $data['lignes']);

            return $devis;
        });
    }

    public function duplicate(Devis $devis): Devis
    {
        return DB::transaction(function () use ($devis) {
            $newDevis = $devis->replicate();
            $newDevis->reference = $this->generateUniqueReference();
            $newDevis->statut = 'Brouillon';
            $newDevis->date_emission = now();
            $newDevis->save();

            foreach ($devis->lignes as $ligne) {
                $newLigne = $ligne->replicate();
                $newLigne->devis_id = $newDevis->id;
                $newLigne->save();
            }

            return $newDevis;
        });
    }

    private function withMontants(array $data, array $lignes): array
    {
        $montantHt = 0;
        foreach ($lignes as $ligne) {
            $montantHt += floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']);
        }

        $data['montant_ht'] = $montantHt;
        $data['montant_tva'] = $montantHt * (floatval($data['taux_tva']) / 100);
        $data['montant_ttc'] = $data['montant_ht'] + $data['montant_tva'];

        unset($data['lignes']);

        return $data;
    }

    private function syncLignes(Devis $devis, array $lignes): void
    {
        foreach ($lignes as $ligne) {
            $devis->lignes()->create([
                'designation' => $ligne['designation'],
                'description' => $ligne['description'] ?? null,
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'montant_ht' => floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']),
            ]);
        }
    }

    /**
     * Génère le PDF du devis et synchronise automatiquement son entrée dans
     * "Mes Documents" (Type=Devis), sans upload manuel côté commercial.
     */
    public function generatePdf(Devis $devis)
    {
        $devis->load(['prospect', 'client', 'lignes', 'commercial']);

        $company = [
            'name' => Setting::get('company_name', 'TechInterv Solutions'),
            'tagline' => Setting::get('company_tagline', ''),
            'address' => Setting::get('company_address', ''),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
            'website' => Setting::get('company_website', ''),
            'ice' => Setting::get('company_ice', ''),
            'rc' => Setting::get('company_rc', ''),
            'logo' => Setting::get('company_logo') ? storage_path('app/public/' . Setting::get('company_logo')) : null,
            'color' => Setting::get('company_color', '#10b981'),
        ];

        $payment = [
            'mode' => Setting::get('payment_mode', ''),
            'delay_days' => Setting::get('payment_delay_days', '30'),
            'deposit_percent' => (float) Setting::get('payment_deposit_percent', '0'),
        ];

        $currency = currency_symbol();
        $legalMentions = Setting::get('legal_mentions', '');

        $pdf = Pdf::loadView('commercial.devis.pdf', compact('devis', 'company', 'payment', 'currency', 'legalMentions'));

        $this->documentService->syncDevisPdf($devis, $pdf->output());

        return $pdf;
    }

    /**
     * Génère une référence unique au format DEV-{ANNEE}-{numéro séquentiel sur 3 chiffres}.
     * Les références existantes (anciens formats) ne sont pas modifiées, seul ce format
     * unique est utilisé pour tout nouveau devis à partir de maintenant.
     */
    private function generateUniqueReference(): string
    {
        $year = now()->format('Y');
        $prefix = "DEV-{$year}-";

        $numero = 1;
        do {
            $reference = $prefix . str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            $numero++;
        } while (Devis::where('reference', $reference)->exists());

        return $reference;
    }
}
