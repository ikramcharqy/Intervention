<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Prospect;
use Illuminate\Support\Facades\DB;

class ProspectConversionService
{
    public function __construct(private ReferenceGeneratorService $referenceGenerator)
    {
    }

    /**
     * Convertit un prospect en client. Réutilisée par la conversion manuelle
     * (ProspectController::convert, déclenchée par le commercial) et la conversion
     * automatique (DevisObserver, déclenchée quand un devis lié au prospect passe
     * à "Accepté"). Idempotente : si ce prospect a déjà un client associé
     * (prospect_id), le retourne au lieu d'en recréer un.
     */
    public function convert(Prospect $prospect, string $origine = 'manuelle'): Client
    {
        return DB::transaction(function () use ($prospect, $origine) {
            $existing = Client::where('prospect_id', $prospect->id)->first();

            if ($existing) {
                if ($prospect->statut !== 'Converti') {
                    $prospect->update(['statut' => 'Converti']);
                }

                return $existing;
            }

            $prospect->update(['statut' => 'Converti']);

            $client = Client::create([
                'code_client' => $this->referenceGenerator->generateClientReference(),
                'type_client' => $prospect->type_prospect,
                'nom' => $prospect->nom_entreprise,
                'nom_contact' => $prospect->nom_contact,
                'email' => $prospect->email,
                'telephone' => $prospect->telephone,
                'adresse_facturation' => $prospect->adresse,
                'ville' => 'À renseigner',
                'commercial_id' => $prospect->commercial_id,
                'prospect_id' => $prospect->id,
                'is_active' => true,
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name ?? 'Système',
                'action' => 'Conversion Prospect → Client',
                'module' => 'Commercial',
                'severity' => 'info',
                'category' => AuditLog::CATEGORY_BUSINESS,
                'ip_address' => app()->runningInConsole() ? null : request()->ip(),
                'details' => "Prospect '{$prospect->nom_entreprise}' converti en Client #{$client->id} ({$client->code_client})"
                    . ($origine === 'automatique' ? ' — conversion automatique suite à devis accepté.' : '.'),
            ]);

            return $client;
        });
    }
}
