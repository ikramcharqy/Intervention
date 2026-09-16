<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientActivite extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'client_id',
        'user_id',
        'type_action',
        'description',
        'donnees_avant',
        'donnees_apres',
    ];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
