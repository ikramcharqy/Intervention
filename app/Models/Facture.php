<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $table = 'factures';

    const STATUT_BROUILLON = 'Brouillon';
    const STATUT_ENVOYEE = 'Envoyée';
    const STATUT_PAYEE = 'Payée';
    const STATUT_PARTIELLEMENT_PAYEE = 'Partiellement payée';
    const STATUT_EN_RETARD = 'En retard';
    const STATUT_ANNULEE = 'Annulée';

    protected $fillable = [
        'reference',
        'client_id',
        'devis_id',
        'commercial_id',
        'statut',
        'date_emission',
        'date_echeance',
        'taux_tva',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'montant_paye',
        'observations',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_echeance' => 'date',
        'taux_tva' => 'float',
        'montant_ht' => 'float',
        'montant_tva' => 'float',
        'montant_ttc' => 'float',
        'montant_paye' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function soldeRestant(): float
    {
        return max(0, $this->montant_ttc - $this->montant_paye);
    }
}
