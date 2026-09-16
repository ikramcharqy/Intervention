<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES_PROSPECT = ['Entreprise', 'Particulier'];

    protected $fillable = [
        'nom_entreprise',
        'type_prospect',
        'nom_contact',
        'email',
        'telephone',
        'adresse',
        'statut',
        'observations',
        'commercial_id',
        'notes',
        'historique',
        'prochaine_action_date',
        'prochaine_action_description',
    ];

    protected $casts = [
        'notes' => 'array',
        'historique' => 'array',
        'prochaine_action_date' => 'date',
    ];

    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    /**
     * Type(s) de besoin technique identifié(s) chez ce prospect (un ou plusieurs).
     */
    public function typesIntervention()
    {
        return $this->belongsToMany(TypeIntervention::class, 'prospect_type_intervention');
    }

    public function activites()
    {
        return $this->hasMany(ProspectActivite::class)->latest();
    }

    /**
     * "En retard" : prochaine action programmée dont la date est déjà passée.
     */
    public function getProchaineActionEnRetardAttribute(): bool
    {
        return $this->prochaine_action_date && $this->prochaine_action_date->isPast() && !$this->prochaine_action_date->isToday();
    }

    /**
     * "Imminente" : prochaine action programmée aujourd'hui ou demain.
     */
    public function getProchaineActionImminenteAttribute(): bool
    {
        return $this->prochaine_action_date
            && !$this->prochaine_action_en_retard
            && $this->prochaine_action_date->lessThanOrEqualTo(now()->addDay()->endOfDay());
    }
}
