<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
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
        ];
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
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}