<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'formulaire_id',
        'question',
        'type_reponse',
        'obligatoire',
        'ordre',
        'placeholder',
        'valeur_par_defaut',
        'condition_affichage', // JSON — réservé pour conditions futures
    ];

    protected $casts = [
        'obligatoire'        => 'boolean',
        'condition_affichage' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Formulaire parent
    public function formulaire()
    {
        return $this->belongsTo(Formulaire::class);
    }

    // Choix prédéfinis (pour Liste, Checkbox, Radio)
    public function choix()
    {
        return $this->hasMany(ChoixQuestion::class)->orderBy('ordre');
    }

    // Réponses saisies par les techniciens
    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Utilitaires
    |--------------------------------------------------------------------------
    */

    // Indique si ce type de champ nécessite des choix prédéfinis
    public function necessiteChoix(): bool
    {
        return in_array($this->type_reponse, Formulaire::TYPES_AVEC_CHOIX);
    }

    // Indique si ce type accepte un fichier
    public function estTypeFichier(): bool
    {
        return in_array($this->type_reponse, Formulaire::TYPES_FICHIER);
    }
}