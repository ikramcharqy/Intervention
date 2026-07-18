<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEntreprise extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'ice',
        'if',
        'rc',
        'patente',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
