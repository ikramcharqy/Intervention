<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\DemandeIntervention;
use App\Models\Facture;
use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\Client;
use App\Services\InterventionService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private InterventionService $interventionService,
    ) {
    }

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
                'demandes_en_attente' => 0,
                'planifiees' => 0,
                'en_cours' => 0,
                'terminees' => 0,
                'trend_interventions' => 0,
                'trend_terminees' => 0,
            ];
            $dernierRapport = null;
            $recentInterventions = collect();
            $parStatut = collect();
            $evolution = ['labels' => [], 'creees' => [], 'terminees' => []];
            $facturation = ['total' => 0, 'solde' => 0];
            $prochaines = collect();
            return view('client.dashboard', compact('stats', 'dernierRapport', 'recentInterventions', 'parStatut', 'evolution', 'facturation', 'prochaines'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');

        // Source unique et partagée avec ClientModule\InterventionController::index() :
        // les deux pages ne peuvent plus diverger, la répartition est calculée une seule fois.
        $parStatut = $this->interventionService->repartitionParStatut($chantierIds);

        $totalInterventions = $parStatut->sum();

        $stats = [
            'chantiers' => $chantierIds->count(),
            'interventions' => $totalInterventions,
            'demandes_en_attente' => DemandeIntervention::where('client_id', $client->id)
                ->where('statut', DemandeIntervention::STATUT_EN_ATTENTE)
                ->count(),
            'planifiees' => $parStatut->get(Intervention::STATUT_PLANIFIEE, 0),
            'en_cours' => $parStatut->get(Intervention::STATUT_EN_COURS, 0),
            'terminees' => $parStatut->get(Intervention::STATUT_TERMINEE, 0)
                + $parStatut->get(Intervention::STATUT_VALIDEE, 0),
        ];

        // Évolution mensuelle réelle (créées/terminées) : alimente le graphique de
        // tendance et les sparklines des cartes KPI, avec un delta mois vs mois précédent.
        $evolution = $this->interventionService->evolutionMensuelle($chantierIds, 6);

        $calculerTendance = function (array $serie): int {
            $dernier = end($serie) ?: 0;
            $precedent = $serie[count($serie) - 2] ?? 0;
            if ($precedent > 0) {
                return (int) round((($dernier - $precedent) / $precedent) * 100);
            }
            return $dernier > 0 ? 100 : 0;
        };

        $stats['trend_interventions'] = $calculerTendance($evolution['creees']);
        $stats['trend_terminees'] = $calculerTendance($evolution['terminees']);

        $facturation = [
            'total' => Facture::where('client_id', $client->id)->sum('montant_ttc'),
            'solde' => Facture::where('client_id', $client->id)->get()->sum(fn ($f) => $f->soldeRestant()),
        ];

        $dernierRapport = Rapport::whereHas('intervention', function ($q) use ($chantierIds) {
            $q->whereIn('chantier_id', $chantierIds);
        })->latest()->first();

        $recentInterventions = $this->interventionService->pourChantiers($chantierIds)
            ->with(['chantier', 'technicien', 'typeIntervention'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        // Panneau "à suivre" : mélange demandes en attente + interventions planifiées
        // les plus proches, données déjà chargées ailleurs, pas de nouvelle requête lourde.
        $prochaines = $this->interventionService->pourChantiers($chantierIds)
            ->with('chantier')
            ->where('statut', Intervention::STATUT_PLANIFIEE)
            ->orderBy('date_prevue_debut')
            ->limit(4)
            ->get();

        return view('client.dashboard', compact('stats', 'dernierRapport', 'recentInterventions', 'parStatut', 'client', 'evolution', 'facturation', 'prochaines'));
    }
}
