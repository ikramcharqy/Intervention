<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'date_debut',
        'date_fin',
        'commentaire',
        'signature_client',
        'signature_technicien',
        'pdf_path',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }
}