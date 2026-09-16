<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Facture;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FactureService
{
    public function __construct(
        private DocumentService $documentService,
    ) {
    }

    /**
     * Crée une facture à partir d'un devis accepté, en reprenant ses montants.
     * Le devis reste la source de vérité des prestations ; la facture ne duplique
     * pas les lignes, elle référence le devis (devis_id) pour le détail.
     */
    public function createFromDevis(Devis $devis): Facture
    {
        if (!in_array($devis->statut, ['Accepté', 'Accepte'], true)) {
            throw new \RuntimeException('Seul un devis accepté peut être facturé.');
        }

        if ($devis->factures()->exists()) {
            throw new \RuntimeException('Une facture existe déjà pour ce devis.');
        }

        return DB::transaction(function () use ($devis) {
            return Facture::create([
                'reference' => $this->generateUniqueReference(),
                'client_id' => $devis->client_id,
                'devis_id' => $devis->id,
                'commercial_id' => $devis->commercial_id,
                'statut' => Facture::STATUT_BROUILLON,
                'date_emission' => now(),
                'date_echeance' => now()->addDays(30),
                'taux_tva' => $devis->taux_tva,
                'montant_ht' => $devis->montant_ht,
                'montant_tva' => $devis->montant_tva,
                'montant_ttc' => $devis->montant_ttc,
            ]);
        });
    }

    public function marquerPayee(Facture $facture, ?float $montant = null): Facture
    {
        $facture->montant_paye = $montant ?? $facture->montant_ttc;
        $facture->statut = $facture->montant_paye >= $facture->montant_ttc
            ? Facture::STATUT_PAYEE
            : Facture::STATUT_PARTIELLEMENT_PAYEE;
        $facture->save();

        return $facture;
    }

    /**
     * Génère le PDF de la facture et synchronise automatiquement son entrée dans
     * "Mes Documents" (Type=Facture), sur le même principe que DevisService::generatePdf.
     */
    public function generatePdf(Facture $facture)
    {
        $facture->load(['client', 'devis.lignes', 'commercial']);

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

        $currency = currency_symbol();

        $pdf = Pdf::loadView('commercial.factures.pdf', compact('facture', 'company', 'currency'));

        $this->documentService->syncFacturePdf($facture, $pdf->output());

        return $pdf;
    }

    /**
     * Génère une référence unique au format FAC-{ANNEE}-{numéro séquentiel sur 3 chiffres},
     * même convention que DevisService::generateUniqueReference (préfixe FAC au lieu de DEV).
     */
    private function generateUniqueReference(): string
    {
        $year = now()->format('Y');
        $prefix = "FAC-{$year}-";

        $numero = 1;
        do {
            $reference = $prefix . str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            $numero++;
        } while (Facture::where('reference', $reference)->exists());

        return $reference;
    }
}
