<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',

        'parent_id',

        'ordre',

        // Nouveau
        'duree_estimee',
        'is_obligatoire',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_obligatoire' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Tâche parente
    public function parent()
    {
        return $this->belongsTo(Tache::class, 'parent_id');
    }

    // Sous-tâches
    public function enfants()
    {
        return $this->hasMany(Tache::class, 'parent_id');
    }

    // Interventions utilisant cette tâche
    public function interventions()
    {
        return $this->hasMany(InterventionTache::class);
    }
}