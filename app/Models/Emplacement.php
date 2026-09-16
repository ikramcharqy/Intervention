<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Emplacement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chantier_id',
        'nom',
        'description',
        'ordre_affichage',
        'latitude',
        'longitude',
        'qr_code',
        'nfc_uid',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ordre_affichage' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    public function techniciens()
    {
        return $this->belongsToMany(
            User::class,
            'emplacement_techniciens',
            'emplacement_id',
            'technicien_id'
        )->withPivot('is_principal')
        ->withTimestamps();
    }
}
