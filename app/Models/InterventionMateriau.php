<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionMateriau extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'materiau_id',

        'quantite',
        'unite',

        'commentaire',

        // Nouveau
        'is_valide',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'is_valide' => 'boolean',
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

    // Matériau utilisé
    public function materiau()
    {
        return $this->belongsTo(Materiau::class);
    }
}