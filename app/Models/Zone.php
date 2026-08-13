<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'chantier_id',
        'nom',
        'code_zone',
        'description',
        'qr_code',
        'nfc_uid',
        'is_active',
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
            'zone_techniciens',
            'zone_id',
            'technicien_id'
        )->withTimestamps();
    }
}

