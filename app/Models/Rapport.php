<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    const STATUT_VALIDATION_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDATION_VALIDE = 'valide';

    protected $fillable = [
        'intervention_id',

        'travaux_effectues',
        'observations',
        'recommandations',
        'statut_equipement',
        'qrcode_scanne',

        'date_debut',
        'date_fin',

        'commentaire',

        // Nouveaux champs
        'duree_reelle',
        'pourcentage_global',
        'statut_validation',
        'validated_at',
        'validated_by',

        'signature_client',
        'signature_technicien',

        'pdf_path',

        // GPS et Meta Photos
        'gps_latitude',
        'gps_longitude',
        'gps_adresse',
        'photos_meta',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',

        'duree_reelle' => 'integer',
        'pourcentage_global' => 'integer',
        'validated_at' => 'datetime',

        'gps_latitude'  => 'decimal:7',
        'gps_longitude' => 'decimal:7',
        'photos_meta'   => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Intervention concernée
    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    // Photos du rapport
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    // Vidéos du rapport
    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    // Documents du rapport
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Réponses au formulaire
    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }

    // Utilisateur (Client) ayant validé le rapport
    public function validateur()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function estValide(): bool
    {
        return $this->statut_validation === self::STATUT_VALIDATION_VALIDE;
    }
}