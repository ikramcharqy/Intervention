<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeReaffectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'technicien_id',
        'admin_id',
        'nouveau_technicien_id',
        'motif',
        'statut',
        'commentaire_admin',
        'date_traitement',
    ];

    protected $casts = [
        'date_traitement' => 'datetime',
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

    // Technicien demandeur
    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    // Admin traitant la demande
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Nouveau technicien proposé
    public function nouveauTechnicien()
    {
        return $this->belongsTo(User::class, 'nouveau_technicien_id');
    }
}
