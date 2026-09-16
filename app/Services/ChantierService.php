<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Document;
use App\Models\Intervention;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ChantierService
{
    public function __construct(private ReferenceGeneratorService $referenceGenerator)
    {
    }

    /**
     * Crée un chantier. `code_chantier` est optionnel côté formulaire (StoreChantierRequest
     * l'accepte nullable) — auto-généré via ReferenceGeneratorService quand non fourni,
     * au lieu de laisser échouer la création (ChantierController::store() appelait cette
     * méthode alors qu'elle n'existait pas encore — bug corrigé au passage).
     */
    public function createChantier(array $data): Chantier
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['code_chantier'])) {
                $client = Client::find($data['client_id']);
                $data['code_chantier'] = $this->referenceGenerator->generateChantierReference($client);
            }

            return Chantier::create($data);
        });
    }

    /**
     * Met à jour un chantier existant. Ne régénère jamais `code_chantier` (référence déjà
     * communiquée le cas échéant) — seuls les champs fournis sont modifiés.
     */
    public function updateChantier(Chantier $chantier, array $data): Chantier
    {
        $chantier->update($data);

        return $chantier;
    }

    /**
     * Génère la fiche PDF d'un chantier (usage interne client), sur le même
     * principe que DevisService::generatePdf / FactureService::generatePdf.
     */
    public function generatePdf(Chantier $chantier)
    {
        $chantier->load(['client.contacts', 'interventions' => fn ($q) => $q->latest('date_prevue_debut')->limit(20), 'interventions.technicien', 'interventions.typeIntervention']);

        $company = [
            'name' => Setting::get('company_name', 'TechInterv Solutions'),
            'tagline' => Setting::get('company_tagline', ''),
            'address' => Setting::get('company_address', ''),
            'phone' => Setting::get('company_phone', ''),
            'email' => Setting::get('company_email', ''),
            'logo' => Setting::get('company_logo') ? storage_path('app/public/' . Setting::get('company_logo')) : null,
            'color' => Setting::get('company_color', '#10b981'),
        ];

        return Pdf::loadView('client.chantiers.pdf', compact('chantier', 'company'));
    }

    /**
     * Un technicien ne peut consulter que les chantiers où il a (ou a eu) au
     * moins une intervention assignée — même périmètre d'accès que celui déjà
     * appliqué implicitement par `GET /api/interventions` (filtré par
     * technicien_id), pas une règle nouvelle inventée pour cet écran.
     */
    public function estAccessiblePourTechnicien(Chantier $chantier, int $technicienId): bool
    {
        return Intervention::where('chantier_id', $chantier->id)
            ->where('technicien_id', $technicienId)
            ->exists();
    }

    /**
     * Mêmes documents que ceux comptés par ClientModule\ChantierController::show()
     * (Document::where('chantier_id', ...) OU rattachés à un rapport d'une
     * intervention de ce chantier) — ici on renvoie la liste, pas seulement le
     * compte, pour permettre la consultation depuis l'app Technicien.
     */
    public function documentsPourChantier(Chantier $chantier): Collection
    {
        return Document::where('chantier_id', $chantier->id)
            ->orWhereHas('rapport.intervention', fn ($q) => $q->where('chantier_id', $chantier->id))
            ->get();
    }
}
