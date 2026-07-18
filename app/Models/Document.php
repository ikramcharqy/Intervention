<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',
        'nom_original',
        'chemin',
        'type_mime',
    ];

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }
}