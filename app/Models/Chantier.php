<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chantier extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES_LOCAL = [
        'Maison', 'Appartement', 'Magasin', 'Bureau', 'Usine',
        'Restaurant', 'Hotel', 'Hopital', 'Ecole', 'Administration',
        'Entrepôt', 'Local technique', 'Site industriel', 'Commerce',
        'Société', 'Entreprise', 'Autre',
    ];

    const VILLES_PRINCIPALES = [
        'Casablanca', 'Rabat', 'Mohammedia', 'Marrakech', 'Tanger', 'Fès',
        'Salé', 'Meknès', 'Agadir', 'Oujda', 'Kénitra', 'Tétouan', 'Safi',
        'El Jadida', 'Nador', 'Béni Mellal',
    ];

    protected $fillable = [
        'client_id',
        'code_chantier',
        'nom',
        'type_local',
        'adresse',
        'ville',
        'latitude',
        'longitude',
        'responsable',
        'telephone_responsable',
        'email_responsable',
        'description',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function emplacements()
    {
        return $this->hasMany(Emplacement::class)->orderBy('ordre_affichage')->orderBy('nom');
    }
}
