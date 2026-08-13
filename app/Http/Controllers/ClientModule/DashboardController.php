<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\Client;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    public function index(): View
    {
        $client = $this->getClient();

        if (!$client) {
            $stats = [
                'chantiers' => 0,
                'interventions' => 0,
                'planifiees' => 0,
                'en_cours' => 0,
                'terminees' => 0,
            ];
            $dernierRapport = null;
            $recentInterventions = collect();
            $parStatut = [];
            return view('client.dashboard', compact('stats', 'dernierRapport', 'recentInterventions', 'parStatut'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        $interventionsQuery = Intervention::whereIn('chantier_id', $chantierIds);

        $stats = [
            'chantiers' => $chantierIds->count(),
            'interventions' => (clone $interventionsQuery)->count(),
            'planifiees' => (clone $interventionsQuery)->where('statut', Intervention::STATUT_PLANIFIEE)->count(),
            'en_cours' => (clone $interventionsQuery)->where('statut', Intervention::STATUT_EN_COURS)->count(),
            'terminees' => (clone $interventionsQuery)->where('statut', Intervention::STATUT_TERMINEE)->count(),
        ];

        $dernierRapport = Rapport::whereHas('intervention', function ($q) use ($chantierIds) {
            $q->whereIn('chantier_id', $chantierIds);
        })->latest()->first();

        $recentInterventions = Intervention::with(['chantier', 'technicien', 'typeIntervention'])
            ->whereIn('chantier_id', $chantierIds)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $parStatut = [
            'Planifiée' => $stats['planifiees'],
            'En cours'  => $stats['en_cours'],
            'Terminée'  => $stats['terminees'],
            'Autres'    => max(0, $stats['interventions'] - ($stats['planifiees'] + $stats['en_cours'] + $stats['terminees'])),
        ];

        return view('client.dashboard', compact('stats', 'dernierRapport', 'recentInterventions', 'parStatut', 'client'));
    }
}
