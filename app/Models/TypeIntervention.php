<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeIntervention extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
        'duree_estimee',

        // Nouveau
        'mode_suivi_defaut',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Un type possède plusieurs interventions
    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    // Un type possède plusieurs formulaires
    public function formulaire()
    {
        return $this->hasOne(Formulaire::class);
    }
}