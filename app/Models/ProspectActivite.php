<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectActivite extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'prospect_id',
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

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
