<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;//pour utiliser les factories 
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

    /**
     * Compte utilisateur du portail Client lié à ce client (users.client_id —
     * même lien que ClientModule\DashboardController::getClient()). Référencé
     * par InterventionService/DemandeInterventionController/NotificationSeeder
     * pour notifier le client ; manquait du modèle bien qu'appelé à 3 endroits.
     */
    public function utilisateurPortail(): ?User
    {
        return User::where('client_id', $this->id)->first();
    }
}
