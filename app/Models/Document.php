<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    const TYPES = [
        'Contrat',
        'Pièce d\'identité',
        'Devis',
        'Facture',
        'Fiche technique',
        'Autre',
    ];

    protected $fillable = [
        'rapport_id',
        'user_id',
        'prospect_id',
        'client_id',
        'devis_id',
        'facture_id',
        'chantier_id',
        'type_document',
        'nom_original',
        'chemin',
        'type_mime',
    ];

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    /**
     * Entité liée (parmi prospect/client/devis/chantier, un seul rempli à la fois)
     * et son libellé d'affichage, pour éviter de dupliquer ce @if/@elseif dans les vues.
     */
    public function entiteLiee(): ?array
    {
        if ($this->prospect) {
            return ['type' => 'Prospect', 'label' => $this->prospect->nom_entreprise];
        }
        if ($this->client) {
            return ['type' => 'Client', 'label' => $this->client->nom];
        }
        if ($this->devis) {
            return ['type' => 'Devis', 'label' => $this->devis->reference];
        }
        if ($this->facture) {
            return ['type' => 'Facture', 'label' => $this->facture->reference];
        }
        if ($this->chantier) {
            return ['type' => 'Chantier', 'label' => $this->chantier->nom];
        }

        return null;
    }
}
