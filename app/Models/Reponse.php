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
    ];

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function choixQuestion()
    {
        return $this->belongsTo(ChoixQuestion::class);
    }
}