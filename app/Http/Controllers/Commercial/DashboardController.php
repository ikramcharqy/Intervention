<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Chantier;
use App\Models\DemandeIntervention;
use App\Models\Prospect;
use App\Models\Devis;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $commercialId = auth()->id();

        $nbProspects = Prospect::count();
        $nbClients = Client::count();
        $nbDevis = Devis::count();
        $devisAttente = Devis::whereIn('statut', ['Envoyé', 'En attente', 'Brouillon'])->count();
        $devisAcceptes = Devis::whereIn('statut', ['Accepté', 'Accepte', 'Validé'])->count();
        $devisRefuses = Devis::whereIn('statut', ['Refusé', 'Refuse', 'Annulé'])->count();

        $prospectsConvertis = Prospect::whereIn('statut', ['Converti', 'Client'])->count();
        $tauxConversion = $nbProspects > 0 ? round(($prospectsConvertis / $nbProspects) * 100, 1) : 0;

        $stats = [
            'prospects' => $nbProspects,
            'clients' => $nbClients,
            'devis' => $nbDevis,
            'devis_attente' => $devisAttente,
            'devis_acceptes' => $devisAcceptes,
            'devis_refuses' => $devisRefuses,
            'taux_conversion' => $tauxConversion,
        ];

        $recentProspects = Prospect::latest()->limit(5)->get();
        $recentDevis = Devis::with('prospect')->latest()->limit(5)->get();

        $prospectsRecents = $recentProspects;
        $devisRecents = $recentDevis;

        return view('commercial.dashboard', compact('stats', 'recentProspects', 'recentDevis', 'prospectsRecents', 'devisRecents'));
    }
}
