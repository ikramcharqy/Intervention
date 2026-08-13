<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $table = 'devis';

    protected $fillable = [
        'reference',
        'prospect_id',
        'client_id',
        'commercial_id',
        'statut',
        'date_emission',
        'date_expiration',
        'taux_tva',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'observations',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'taux_tva' => 'float',
        'montant_ht' => 'float',
        'montant_tva' => 'float',
        'montant_ttc' => 'float',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function lignes()
    {
        return $this->hasMany(DevisLigne::class, 'devis_id');
    }
}
