<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmplacementTechnicien extends Model
{
    protected $table = 'emplacement_techniciens';

    protected $fillable = [
        'technicien_id',
        'emplacement_id',
        'is_principal',
    ];
}
