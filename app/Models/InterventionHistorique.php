<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionHistorique extends Model
{
    use HasFactory;

    protected $table = 'intervention_historiques';

    protected $fillable = [
        'intervention_id',
        'user_id',
        // ancienne et nouvelle colonnes selon migrations historiques
        'ancien_statut',
        'nouveau_statut',
        'statut_avant',
        'statut_apres',
        // commentaire / motif
        'action',
        'motif',
        'commentaire',
        // meta
        'metadata',
        'ip_address',
        'device_id',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    // Fournit un accès normalisé au statut avant/après pour compatibilité
    public function getAncienStatutAttribute()
    {
        return $this->attributes['ancien_statut'] ?? $this->attributes['statut_avant'] ?? null;
    }

    public function getNouveauStatutAttribute()
    {
        return $this->attributes['nouveau_statut'] ?? $this->attributes['statut_apres'] ?? null;
    }

    public function getMotifAttribute()
    {
        return $this->attributes['motif'] ?? $this->attributes['commentaire'] ?? null;
    }

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

    // Utilisateur ayant déclenché la transition
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
