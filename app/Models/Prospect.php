<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom_entreprise',
        'nom_contact',
        'email',
        'telephone',
        'adresse',
        'statut',
        'observations',
        'commercial_id',
        'notes',
        'historique',
    ];

    protected $casts = [
        'notes' => 'array',
        'historique' => 'array',
    ];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }
}
