<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InterventionMateriau;

class Materiau extends Model
{
    use HasFactory, SoftDeletes;

    const CATEGORIES = [
        'Câblage',
        'Équipement réseau',
        'Vidéosurveillance',
        'Connectique',
        'Outillage',
        'Autre',
    ];

    protected $fillable = [
        'reference',
        'qr_code',
        'nom',
        'description',
        'categorie',
        'marque',
        'modele_fabricant',
        'unite',
        'prix_unitaire',
        'stock',
        'seuil_alerte',
        'image_path',
        'fiche_technique_path',
        'is_active',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'stock'         => 'decimal:2',
        'seuil_alerte'  => 'decimal:2',
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

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accesseurs
    |--------------------------------------------------------------------------
    */

    public function estEnStockBas(): bool
    {
        return (float) $this->stock <= (float) $this->seuil_alerte;
    }
}