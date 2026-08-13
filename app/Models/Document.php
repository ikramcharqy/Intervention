<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',
        'user_id',
        'prospect_id',
        'client_id',
        'nom_original',
        'chemin',
        'type_mime',
    ];

    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}