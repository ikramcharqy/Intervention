<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneTechnicien extends Model
{
    use HasFactory;

    protected $table = 'zone_techniciens';

    protected $fillable = [
        'zone_id',
        'technicien_id',
        'is_principal',
    ];

    protected $casts = [
        'is_principal' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Zone concernée
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    // Technicien affecté
    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}