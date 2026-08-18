<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulaire extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Types de champs supportés
    |--------------------------------------------------------------------------
    */
    const TYPES_CHAMPS = [
        'Texte'     => 'Texte court',
        'TexteLong' => 'Texte long',
        'Nombre'    => 'Nombre',
        'Date'      => 'Date',
        'Heure'     => 'Heure',
        'DateHeure' => 'Date et Heure',
        'OuiNon'    => 'Oui / Non',
        'Liste'     => 'Liste déroulante',
        'Checkbox'  => 'Cases à cocher (Checkbox)',
        'Radio'     => 'Boutons radio',
        'Photo'     => 'Photo',
        'Signature' => 'Signature',
        'Document'  => 'Document',
        'GPS'       => 'Coordonnées GPS',
        'QRCode'    => 'QR Code',
        'Materiaux' => 'Matériaux utilisés',
    ];

    // Types nécessitant des choix prédéfinis
    const TYPES_AVEC_CHOIX = ['Liste', 'Checkbox', 'Radio'];

    // Types qui acceptent des fichiers
    const TYPES_FICHIER = ['Photo', 'Signature', 'Document'];

    protected $fillable = [
        'type_intervention_id',
        'nom',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Type d'intervention associé (1 formulaire par type max)
    public function typeIntervention()
    {
        return $this->belongsTo(TypeIntervention::class);
    }

    // Champs/questions du formulaire, ordonnés
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('ordre');
    }

    // Questions actives uniquement
    public function questionsActives()
    {
        return $this->hasMany(Question::class)->orderBy('ordre');
    }
}