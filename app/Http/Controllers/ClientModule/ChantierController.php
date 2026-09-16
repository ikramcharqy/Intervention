<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Document;
use App\Services\ChantierService;
use App\Services\InterventionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChantierController extends Controller
{
    public function __construct(
        private InterventionService $interventionService,
        private ChantierService $chantierService,
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

    public function index(Request $request): View
    {
        $client = $this->getClient();
        $recherche = $request->input('q');

        if (!$client) {
            $chantiers = collect();
            return view('client.chantiers.index', compact('chantiers', 'recherche'));
        }

        $query = Chantier::withCount('interventions')
            ->with(['interventions' => fn($q) => $q->latest('updated_at')])
            ->where('client_id', $client->id);

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'like', "%{$recherche}%")
                    ->orWhere('ville', 'like', "%{$recherche}%");
            });
        }

        $chantiers = $query->orderBy('nom')->paginate(12)->withQueryString();

        return view('client.chantiers.index', compact('chantiers', 'recherche'));
    }

    public function show(Chantier $chantier, Request $request): View
    {
        $client = $this->getClient();

        // Sécurité : vérifier que le chantier appartient bien au client connecté
        if (!$client || $chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce chantier.');
        }

        $chantier->load(['emplacements', 'client.contacts']);

        $statutFiltre = $request->input('statut', 'tous');
        $tri = $request->input('tri', 'date_desc');

        // Même source que le Tableau de Bord / Mes Interventions (InterventionService::pourChantiers).
        $query = $this->interventionService->pourChantiers(collect([$chantier->id]))
            ->with(['technicien', 'typeIntervention']);

        if ($statutFiltre && $statutFiltre !== 'tous') {
            $query->where('statut', $statutFiltre);
        }

        match ($tri) {
            'date_asc' => $query->orderBy('date_prevue_debut', 'asc'),
            'statut' => $query->orderBy('statut'),
            default => $query->orderBy('date_prevue_debut', 'desc'),
        };

        $interventions = $query->paginate(10)->withQueryString();

        $documentsCount = Document::where('chantier_id', $chantier->id)
            ->orWhere(function ($q) use ($client, $chantier) {
                $q->where('client_id', $client->id)
                    ->whereHas('rapport.intervention', fn ($qi) => $qi->where('chantier_id', $chantier->id));
            })
            ->count();

        return view('client.chantiers.show', compact('chantier', 'interventions', 'statutFiltre', 'tri', 'documentsCount'));
    }

    public function pdf(Chantier $chantier)
    {
        $client = $this->getClient();

        if (!$client || $chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce chantier.');
        }

        return $this->chantierService->generatePdf($chantier)
            ->stream('chantier_' . ($chantier->code_chantier ?? $chantier->id) . '.pdf');
    }
}
