<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeIntervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'commercial_id',
        'client_id',
        'chantier_id',
        'type_intervention_id',
        'priorite',
        'statut',
        'objet',
        'description',
        'photos',
        'documents',
    ];

    protected $casts = [
        'photos' => 'array',
        'documents' => 'array',
    ];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class);
    }

    public function typeIntervention()
    {
        return $this->belongsTo(TypeIntervention::class);
    }
}
