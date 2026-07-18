<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Session de suivi GPS continu pendant le déplacement d'un technicien
 * lors d'une intervention. Module indépendant de TrackingSession
 * (qui gère le pointage ponctuel de présence GPS/QR/NFC/Manuel).
 */
class GpsTrackingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'technicien_id',
        'started_at',
        'ended_at',
        'distance_metres',
    ];

    protected $casts = [
        'started_at'      => 'datetime',
        'ended_at'        => 'datetime',
        'distance_metres' => 'decimal:2',
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

    public function points()
    {
        return $this->hasMany(GpsTrackingPoint::class)->orderBy('captured_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Accesseurs d'affichage
    |--------------------------------------------------------------------------
    */

    /**
     * Retourne true si la session est toujours active (non arrêtée).
     */
    public function estActive(): bool
    {
        return is_null($this->ended_at);
    }

    /**
     * Durée de la session en secondes (jusqu'à ended_at, ou jusqu'à maintenant si active).
     */
    public function dureeSecondes(): int
    {
        $fin = $this->ended_at ?? now();
        return max(0, $this->started_at->diffInSeconds($fin));
    }
}
