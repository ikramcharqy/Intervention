<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterventionController extends Controller
{
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

        if (!$client) {
            $interventions = collect();
            $statutFiltre = $request->input('statut', 'tous');
            return view('client.interventions.index', compact('interventions', 'statutFiltre'));
        }

        $chantierIds = Chantier::where('client_id', $client->id)->pluck('id');
        $statutFiltre = $request->input('statut', 'tous');

        $query = Intervention::with(['chantier', 'technicien', 'typeIntervention'])
            ->whereIn('chantier_id', $chantierIds);

        if ($statutFiltre && $statutFiltre !== 'tous') {
            $query->where('statut', $statutFiltre);
        }

        $interventions = $query->orderBy('date_prevue_debut', 'desc')->paginate(15)->withQueryString();

        return view('client.interventions.index', compact('interventions', 'statutFiltre'));
    }

    public function show(Intervention $intervention): View
    {
        $client = $this->getClient();

        // Sécurité : vérification que l'intervention appartient au client
        if (!$client || !$intervention->chantier || $intervention->chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }

        $intervention->load(['chantier', 'emplacement', 'technicien', 'typeIntervention', 'rapport', 'materiaux.materiau', 'taches.tache']);

        return view('client.interventions.show', compact('intervention'));
    }
}
