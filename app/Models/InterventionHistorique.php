<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterventionHistorique extends Model
{
    use HasFactory;

    protected $table = 'intervention_historiques';

    protected $fillable = [
        'intervention_id',
        'user_id',
        'statut_avant',
        'statut_apres',
        'commentaire',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Intervention concernée
    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    // Utilisateur ayant déclenché la transition
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
