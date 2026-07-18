<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',
        'chemin',
        'description',
    ];

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }
}