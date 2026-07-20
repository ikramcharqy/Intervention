<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'fonction',
        'is_principal',
    ];

    protected $casts = [
        'is_principal' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
