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
        'latitude',
        'longitude',
        'qr_code_scan',
        'nfc_uid_scan',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'latitude'   => 'decimal:7',
        'longitude'  => 'decimal:7',
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Intervention liée à la session
    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    // Technicien ayant déclenché la session
    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }
}
