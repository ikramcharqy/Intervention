<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',

        'nom_original',

        'chemin',

        'description',
        'type_photo', // 'avant', 'apres', 'probleme'

        // Nouveau
        'latitude',
        'longitude',
        'date_prise',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'date_prise' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }
}