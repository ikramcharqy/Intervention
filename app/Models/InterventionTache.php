<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionTache extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'tache_id',
        'statut',
        'date_debut',
        'date_fin',
        'duree',
        'pourcentage',
        'commentaire',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    public function tache()
    {
        return $this->belongsTo(Tache::class);
    }
}