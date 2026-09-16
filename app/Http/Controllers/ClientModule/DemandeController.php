<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DemandeIntervention;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemandeController extends Controller
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
        $statutFiltre = $request->input('statut', 'tous');
        $periode = $request->input('periode', '');

        if (!$client) {
            $demandes = collect();
            return view('client.demandes.index', compact('demandes', 'statutFiltre', 'periode'));
        }

        $query = DemandeIntervention::with(['chantier', 'typeIntervention', 'intervention'])
            ->where('client_id', $client->id);

        // Filtre sur le libellé calculé côté client (DemandeIntervention::libelleStatutClient) :
        // traduit ici en conditions composites, la donnée en base ne portant qu'un statut
        // brut (En attente / Acceptee / Refusee) qui ne distingue pas "en attente de
        // validation" de "convertie".
        match ($statutFiltre) {
            'soumise' => $query->where('statut', DemandeIntervention::STATUT_EN_ATTENTE),
            'en_attente_validation' => $query->where('statut', DemandeIntervention::STATUT_ACCEPTEE)->whereDoesntHave('intervention'),
            'convertie' => $query->where('statut', DemandeIntervention::STATUT_ACCEPTEE)->whereHas('intervention'),
            'refusee' => $query->where('statut', DemandeIntervention::STATUT_REFUSEE),
            default => null,
        };

        if ($periode !== '') {
            $query->where('created_at', '>=', now()->subDays((int) $periode));
        }

        $demandes = $query->latest()->paginate(15)->withQueryString();

        return view('client.demandes.index', compact('demandes', 'statutFiltre', 'periode'));
    }
}
