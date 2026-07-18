<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chantier extends Model
{
    use HasFactory;
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
        'Description',
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
        return $this->hasMany(Emplacement::class);
    }
}
