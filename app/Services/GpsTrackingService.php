<?php

namespace App\Services;

use App\Events\GpsPositionBroadcast;
use App\Models\GpsTrackingPoint;
use App\Models\GpsTrackingSession;
use App\Models\Intervention;
use App\Models\User;
use RuntimeException;

class GpsTrackingService
{
    /**
     * Démarre une nouvelle session de suivi GPS pour une intervention.
     * Une seule session active à la fois par intervention.
     */
    public function startSession(Intervention $intervention, User $technicien): GpsTrackingSession
    {
        $sessionActive = $intervention->gpsTrackingSessions()
            ->whereNull('ended_at')
            ->exists();

        if ($sessionActive) {
            throw new RuntimeException('Une session de suivi GPS est déjà en cours pour cette intervention.');
        }

        return GpsTrackingSession::create([
            'intervention_id' => $intervention->id,
            'technicien_id'   => $technicien->id,
            'started_at'      => now(),
        ]);
    }

    /**
     * Arrête une session de suivi GPS et fige la distance totale.
     */
    public function stopSession(GpsTrackingSession $session): GpsTrackingSession
    {
        if (!$session->estActive()) {
            throw new RuntimeException('Cette session de suivi GPS est déjà arrêtée.');
        }

        $session->ended_at = now();
        $session->distance_metres = $this->calculateDistance($session);
        $session->save();

        return $session;
    }

    /**
     * Enregistre un nouveau point GPS et met à jour la distance cumulée.
     */
    public function addPoint(GpsTrackingSession $session, array $data): GpsTrackingPoint
    {
        if (!$session->estActive()) {
            throw new RuntimeException('Impossible d\'ajouter un point : la session de suivi GPS est arrêtée.');
        }

        $dernierPoint = $session->points()->latest('captured_at')->first();

        $point = $session->points()->create([
            'latitude'    => $data['latitude'],
            'longitude'   => $data['longitude'],
            'captured_at' => $data['captured_at'] ?? now(),
        ]);

        $vitesseKmh = 0.0;

        if ($dernierPoint) {
            $distanceAjoutee = $this->haversineMetres(
                (float) $dernierPoint->latitude,
                (float) $dernierPoint->longitude,
                (float) $point->latitude,
                (float) $point->longitude
            );

            $session->increment('distance_metres', $distanceAjoutee);

            $secondesEcoulees = $dernierPoint->captured_at->diffInSeconds($point->captured_at, false);
            if ($secondesEcoulees > 0) {
                $vitesseKmh = ($distanceAjoutee / $secondesEcoulees) * 3.6;
            }
        }

        $session->refresh();

        try {
            event(new GpsPositionBroadcast($point, $vitesseKmh));
        } catch (\Throwable $e) {
            // La position est déjà enregistrée en base : une panne du serveur
            // temps réel (Reverb indisponible) ne doit jamais faire échouer
            // l'enregistrement du point GPS côté technicien.
            report($e);
        }

        return $point;
    }

    /**
     * Recalcule la distance totale parcourue à partir de tous les points de la session.
     */
    public function calculateDistance(GpsTrackingSession $session): float
    {
        $points = $session->points()->orderBy('captured_at')->get(['latitude', 'longitude']);

        $distance = 0.0;

        for ($i = 1; $i < $points->count(); $i++) {
            $distance += $this->haversineMetres(
                (float) $points[$i - 1]->latitude,
                (float) $points[$i - 1]->longitude,
                (float) $points[$i]->latitude,
                (float) $points[$i]->longitude
            );
        }

        return round($distance, 2);
    }

    /**
     * Distance en mètres entre deux coordonnées GPS (formule de Haversine).
     */
    private function haversineMetres(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $rayonTerre = 6371000; // mètres

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $rayonTerre * $c;
    }
}
