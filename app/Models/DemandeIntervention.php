<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeIntervention extends Model
{
    use HasFactory;

    // Valeurs réellement écrites en base par DemandeInterventionController (statut libre, non contraint).
    const STATUT_EN_ATTENTE = 'En attente';
    const STATUT_ACCEPTEE = 'Acceptee';
    const STATUT_REFUSEE = 'Refusee';

    protected $fillable = [
        'reference',
        'commercial_id',
        'client_id',
        'chantier_id',
        'type_intervention_id',
        'priorite',
        'statut',
        'objet',
        'description',
        'photos',
        'documents',
    ];

    protected $casts = [
        'photos' => 'array',
        'documents' => 'array',
    ];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    public function typeIntervention()
    {
        return $this->belongsTo(TypeIntervention::class);
    }

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function intervention()
    {
        return $this->hasOne(Intervention::class);
    }

    /**
     * Libellé du statut côté portail Client : distingue une demande acceptée
     * mais pas encore convertie d'une demande déjà transformée en Intervention,
     * sans introduire de nouvelle colonne (le statut réel reste "Acceptee").
     */
    public function libelleStatutClient(): string
    {
        if ($this->statut === self::STATUT_ACCEPTEE) {
            return $this->intervention ? 'Convertie en intervention' : 'En attente de validation';
        }

        if ($this->statut === self::STATUT_REFUSEE) {
            return 'Refusée';
        }

        return 'Soumise';
    }

    /**
     * Extrait le motif de refus saisi par le Commercial (DemandeInterventionController::refuser()
     * l'ajoute en texte structuré dans `description`, il n'existe pas de colonne dédiée —
     * on l'expose ici sans modification de schéma ni du contrôleur Commercial existant).
     */
    public function motifRefus(): ?string
    {
        if ($this->statut !== self::STATUT_REFUSEE || !$this->description) {
            return null;
        }

        if (preg_match('/\[REFUS COMMERCIAL[^\]]*\]\s*Motif\s*:\s*(.+)/su', $this->description, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
