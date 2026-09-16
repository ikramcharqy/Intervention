<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'technicien_id',

        'mode',

        'started_at',
        'ended_at',

        'start_latitude',
        'start_longitude',

        'end_latitude',
        'end_longitude',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}