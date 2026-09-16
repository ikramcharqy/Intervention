<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable, HasRoles;
    //notifiable=> permet d'envoyer des notifications à l'utilisateur
    //HasRoles=>permet d'attribuer des rôles et des permissions à l'utilisateur

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'telephone',
        'photo',
        'adresse',
        'password',
        'is_active',
        'en_service',
        'en_service_maj_le',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'en_service' => 'boolean',
            'en_service_maj_le' => 'datetime',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Préférence de notification par catégorie — actuellement une seule
     * catégorie réellement exploitée (`intervention_updates`, cf.
     * ProfileUpdateRequest::prepareForValidation()) ; `true` par défaut tant
     * que l'utilisateur n'a jamais explicitement désactivé cette catégorie
     * (préférence absente ≠ préférence refusée).
     */
    public function souhaiteNotification(string $categorie): bool
    {
        return ($this->notification_preferences[$categorie] ?? true) !== false;
    }

        public function interventions()
    {
        return $this->hasMany(Intervention::class, 'technicien_id');
    }

        public function emplacements()
        {
            return $this->belongsToMany(
                Emplacement::class,
                'emplacement_techniciens',
                'technicien_id',
                'emplacement_id'
            )->withPivot('is_principal')
            ->withTimestamps();
        }

        public function trackingSessions()
    {
        return $this->hasMany(TrackingSession::class, 'technicien_id');
    }

    public function demandesReaffectation()
    {
        return $this->hasMany(
            DemandeReaffectation::class,
            'technicien_id'
        );
    }

    public function validationsReaffectation()
    {
        return $this->hasMany(
            DemandeReaffectation::class,
            'admin_id'
        );
    }

    // Clients gérés par ce commercial
    public function clientsGeres()
    {
        return $this->hasMany(Client::class, 'commercial_id');
    }

        public function client()
    {
        return $this->hasOne(Client::class);
    }
}