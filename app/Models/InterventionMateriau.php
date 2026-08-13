<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Materiau;

class InterventionMateriau extends Model{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'materiau_id',

        'statut',

        'date_debut',
        'date_fin',

        'duree',

        'pourcentage',

        'commentaire',

        'ordre_execution',
        'is_validee',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',

        'duree' => 'integer',
        'pourcentage' => 'integer',

        'is_validee' => 'boolean',
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

    // Tâche concernée
    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }
}