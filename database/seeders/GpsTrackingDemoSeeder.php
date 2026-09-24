<?php

namespace Database\Seeders;

use App\Models\GpsTrackingSession;
use App\Models\Intervention;
use App\Services\GpsTrackingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de démonstration pour le Poste de Contrôle GPS
 * (resources/views/gps.blade.php, route /gps-tracking) : sans lui, la carte
 * Leaflet reste vide ("Aucune intervention avec tracking GPS actif") tant
 * qu'aucun technicien n'a réellement démarré un suivi GPS depuis l'app
 * mobile — aucun autre seeder ne crée de GpsTrackingSession/GpsTrackingPoint.
 *
 * Simule, pour chaque intervention "En cours" existante (notamment celles de
 * TechnicienDemoDataSeeder), un trajet réaliste du technicien qui converge
 * vers les coordonnées du chantier, en réutilisant GpsTrackingService::
 * addPoint() (même logique de calcul de distance/vitesse que le flux réel
 * app mobile → API) plutôt que de dupliquer le calcul haversine.
 *
 * Doit tourner après TechnicienDemoDataSeeder (qui crée les interventions
 * "En cours" de démo) — enregistré en dernier dans DatabaseSeeder.
 * Idempotent : une intervention qui a déjà une session GPS est ignorée.
 */
class GpsTrackingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(GpsTrackingService::class);

        $interventions = Intervention::where('statut', Intervention::STATUT_EN_COURS)
            ->whereNotNull('date_reelle_debut')
            ->whereHas('chantier', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))
            ->whereDoesntHave('gpsTrackingSessions')
            ->with('chantier')
            ->get();

        foreach ($interventions as $intervention) {
            $this->simulerTrajet($service, $intervention);
        }
    }

    private function simulerTrajet(GpsTrackingService $service, Intervention $intervention): void
    {
        $chantier = $intervention->chantier;
        $destLat = (float) $chantier->latitude;
        $destLng = (float) $chantier->longitude;

        // Point de départ simulé : ~2 à 4 km du chantier, direction aléatoire
        // mais déterministe (seedée par l'ID d'intervention) pour rester
        // stable d'un run à l'autre.
        mt_srand($intervention->id);
        $bearing = deg2rad(mt_rand(0, 359));
        $distanceDepartKm = mt_rand(20, 40) / 10; // 2.0 à 4.0 km

        // Approximation plane suffisante à cette échelle (quelques km) : 1° de
        // latitude ≈ 111 km, 1° de longitude ≈ 111 km × cos(latitude).
        $deltaLat = ($distanceDepartKm / 111) * cos($bearing);
        $deltaLng = ($distanceDepartKm / (111 * cos(deg2rad($destLat)))) * sin($bearing);

        $startLat = $destLat + $deltaLat;
        $startLng = $destLng + $deltaLng;

        $debut = $intervention->date_reelle_debut->copy();
        $finMax = $debut->copy()->addHour();
        $now = Carbon::now();
        $fin = $now->lessThan($finMax) ? $now : $finMax;
        $dureeSecondes = max(60, $debut->diffInSeconds($fin));

        $nbPoints = min(14, max(5, intdiv($dureeSecondes, 180)));

        $session = GpsTrackingSession::create([
            'intervention_id' => $intervention->id,
            'technicien_id'   => $intervention->technicien_id,
            'started_at'      => $debut,
        ]);

        // Le technicien parcourt ~85 % du trajet vers le chantier : la
        // mission "En cours" n'est pas encore arrivée à destination, sans
        // quoi elle serait déjà "Terminée".
        $progressionMax = 0.85;

        for ($i = 0; $i < $nbPoints; $i++) {
            $ratio = $progressionMax * ($i / max(1, $nbPoints - 1));

            // Léger zig-zag pour un tracé moins parfaitement rectiligne
            // qu'une interpolation purement linéaire.
            $jitterLat = mt_rand(-15, 15) / 100000;
            $jitterLng = mt_rand(-15, 15) / 100000;

            $lat = $startLat + ($destLat - $startLat) * $ratio + $jitterLat;
            $lng = $startLng + ($destLng - $startLng) * $ratio + $jitterLng;

            $capturedAt = $debut->copy()->addSeconds((int) round($dureeSecondes * ($i / max(1, $nbPoints - 1))));

            $service->addPoint($session, [
                'latitude'    => $lat,
                'longitude'   => $lng,
                'captured_at' => $capturedAt,
            ]);
        }

        mt_srand(); // ne pas polluer l'aléatoire global pour le reste du run
    }
}
