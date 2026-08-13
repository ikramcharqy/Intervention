<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',
        'question_id',
        'choix_question_id',

        'reponse_texte',
        'reponse_nombre',
        'reponse_fichier',

        // Nouveaux champs
        'reponse_date',
        'reponse_heure',
        'reponse_datetime',
        'reponse_boolean',
    ];

    protected $casts = [
        'reponse_date' => 'date',
        'reponse_heure' => 'datetime:H:i',
        'reponse_datetime' => 'datetime',
        'reponse_boolean' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Rapport concerné
    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }

    // Question concernée
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Choix sélectionné
    public function choixQuestion()
    {
        return $this->belongsTo(ChoixQuestion::class);
    }
}
