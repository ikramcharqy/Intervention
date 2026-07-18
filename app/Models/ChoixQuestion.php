<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChoixQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'libelle',   // Texte affiché à l'utilisateur
        'valeur',    // Valeur stockée en base
        'ordre',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }
}