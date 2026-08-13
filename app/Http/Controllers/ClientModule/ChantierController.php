<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Chantier;
use App\Models\Client;
use Illuminate\View\View;

class ChantierController extends Controller
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
            $chantiers = collect();
            return view('client.chantiers.index', compact('chantiers'));
        }

        $chantiers = Chantier::withCount('interventions')
            ->with(['interventions' => fn($q) => $q->latest('updated_at')])
            ->where('client_id', $client->id)
            ->orderBy('nom')
            ->get();

        return view('client.chantiers.index', compact('chantiers'));
    }

    public function show(Chantier $chantier): View
    {
        $client = $this->getClient();

        // Sécurité : vérifier que le chantier appartient bien au client connecté
        if (!$client || $chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à ce chantier.');
        }

        $chantier->load(['emplacements', 'interventions.technicien', 'interventions.typeIntervention']);

        return view('client.chantiers.show', compact('chantier'));
    }
}
