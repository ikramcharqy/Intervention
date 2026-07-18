<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InterventionMateriau;

class Materiau extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'nom',
        'description',
        'unite',
        'prix_unitaire',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'stock'         => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Utilisations du matériau dans les interventions
    public function interventions()
    {
        return $this->hasMany(InterventionMateriau::class);
    }
}