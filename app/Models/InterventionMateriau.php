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
        'quantite',
        'unite',
        'commentaire',
        'is_valide',
    ];

    protected $casts = [
        'quantite'  => 'decimal:2',
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

    // Matériau concerné
    public function materiau()
    {
        return $this->belongsTo(Materiau::class);
    }
}