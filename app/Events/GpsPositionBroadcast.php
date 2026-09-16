<?php

namespace App\Events;

use App\Models\GpsTrackingPoint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class GpsPositionBroadcast implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public int $interventionId;
    public int $technicienId;
    public string $technicienName;
    public float $latitude;
    public float $longitude;
    public string $capturedAt;
    public float $vitesseKmh;
    public float $distanceMetres;

    public function __construct(GpsTrackingPoint $point, float $vitesseKmh)
    {
        $session = $point->session;

        $this->interventionId = $session->intervention_id;
        $this->technicienId = $session->technicien_id;
        $this->technicienName = $session->technicien->name ?? 'Technicien';
        $this->latitude = (float) $point->latitude;
        $this->longitude = (float) $point->longitude;
        $this->capturedAt = $point->captured_at->toIso8601String();
        $this->vitesseKmh = round($vitesseKmh, 1);
        $this->distanceMetres = (float) $session->distance_metres;
    }

    /**
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("intervention.{$this->interventionId}.gps")];
    }

    public function broadcastAs(): string
    {
        return 'position.updated';
    }
}
