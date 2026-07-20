<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;//pour utiliser les factories 
    //pour générer des données de test
         protected $fillable = [
        'code_client',
        'type_client',
        'nom',
        'nom_contact',
        'telephone',
        'telephone_secondaire',
        'email',
        'adresse_facturation',
        'ville',
        'pays',
        'observations',
        'is_active',
        'commercial_id',
    ];

    public function clientEntreprise()
    {
        return $this->hasOne(ClientEntreprise::class);
    }

    public function chantiers()
    {
        return $this->hasMany(Chantier::class);
    }

    // Commercial qui a enregistré ce client
    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    // Contacts du client (CRM)
    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }
}
