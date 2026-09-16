<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSupport extends Model
{
    protected $table = 'ticket_supports';

    const STATUT_OUVERT = 'Ouvert';
    const STATUT_EN_COURS = 'En cours';
    const STATUT_RESOLU = 'Résolu';

    const CATEGORIES = ['Facturation', 'Technique', 'Compte', 'Autre'];

    protected $fillable = [
        'reference',
        'client_id',
        'user_id',
        'commercial_id',
        'categorie',
        'objet',
        'message',
        'statut',
        'piece_jointe',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }
}
