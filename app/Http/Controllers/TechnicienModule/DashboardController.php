<?php

namespace App\Http\Controllers\TechnicienModule;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $technicienId = auth()->id();

        $interventionsAujourdhui = Intervention::with(['chantier', 'typeIntervention', 'emplacement'])
            ->where('technicien_id', $technicienId)
            ->whereDate('date_prevue_debut', today())
            ->get();

        $enCours = Intervention::with(['chantier', 'typeIntervention', 'emplacement'])
            ->where('technicien_id', $technicienId)
            ->where('statut', Intervention::STATUT_EN_COURS)
            ->first();

        $stats = [
            'total' => Intervention::where('technicien_id', $technicienId)->count(),
            'planifiees' => Intervention::where('technicien_id', $technicienId)->where('statut', Intervention::STATUT_PLANIFIEE)->count(),
            'en_cours' => Intervention::where('technicien_id', $technicienId)->where('statut', Intervention::STATUT_EN_COURS)->count(),
            'terminees' => Intervention::where('technicien_id', $technicienId)->where('statut', Intervention::STATUT_TERMINEE)->count(),
        ];

        return view('technicien.dashboard', compact('interventionsAujourdhui', 'enCours', 'stats'));
    }
}
