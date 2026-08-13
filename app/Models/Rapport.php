<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

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

        'signature_client',
        'signature_technicien',

        'pdf_path',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',

        'duree_reelle' => 'integer',
        'pourcentage_global' => 'integer',
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
}