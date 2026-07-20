<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Chantier;
use App\Models\DemandeIntervention;
use App\Models\Prospect;
use Illuminate\View\View;

/**
 * Tableau de bord dédié au rôle Commercial.
 *
 * Ce contrôleur ne fait qu'agréger des compteurs en lecture seule à partir
 * des modèles existants (aucune nouvelle logique métier, aucune écriture
 * en base). Il sert uniquement à alimenter la vue commercial.dashboard.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $commercialId = auth()->id();

        $stats = [
            'prospects' => Prospect::where('commercial_id', $commercialId)->count(),
            'prospects_a_relancer' => Prospect::where('commercial_id', $commercialId)
                ->whereNotIn('statut', ['Converti', 'Perdu'])
                ->count(),
            'clients' => Client::where('commercial_id', $commercialId)->count(),
            'chantiers' => Chantier::whereHas('client', function ($query) use ($commercialId) {
                $query->where('commercial_id', $commercialId);
            })->count(),
            'demandes' => DemandeIntervention::where('commercial_id', $commercialId)->count(),
            'demandes_en_attente' => DemandeIntervention::where('commercial_id', $commercialId)
                ->where('statut', 'En attente')
                ->count(),
        ];

        $prospectsRecents = Prospect::where('commercial_id', $commercialId)
            ->latest()
            ->limit(5)
            ->get();

        $demandesRecentes = DemandeIntervention::with(['client', 'chantier', 'typeIntervention'])
            ->where('commercial_id', $commercialId)
            ->latest()
            ->limit(5)
            ->get();

        return view('commercial.dashboard', compact('stats', 'prospectsRecents', 'demandesRecentes'));
    }
}
