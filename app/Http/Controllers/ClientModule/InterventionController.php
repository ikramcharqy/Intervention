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

    /**
     * Formulaire pour permettre au Client d'émettre une demande d'intervention.
     */
    public function createDemande(): View
    {
        $client = $this->getClient();
        $chantiers = $client ? Chantier::where('client_id', $client->id)->get() : collect();
        $typesIntervention = \App\Models\TypeIntervention::where('is_active', true)->get();

        return view('client.interventions.create_demande', compact('chantiers', 'typesIntervention'));
    }

    /**
     * Enregistre la demande d'intervention émise par le Client.
     */
    public function storeDemande(Request $request)
    {
        $client = $this->getClient();
        if (!$client) {
            abort(403, 'Profil client introuvable.');
        }

        $validated = $request->validate([
            'chantier_id'          => 'required|exists:chantiers,id',
            'type_intervention_id' => 'nullable|exists:type_interventions,id',
            'priorite'             => 'required|string',
            'objet'                => 'required|string|max:255',
            'description'          => 'required|string',
        ]);

        $commercialId = $client->commercial_id;
        if (!$commercialId) {
            $commercialUser = \App\Models\User::role('Commercial')->first() 
                ?? \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'Commercial'); })->first();
            $commercialId = $commercialUser?->id;
        }

        $validated['client_id']     = $client->id;
        $validated['commercial_id'] = $commercialId;
        $validated['reference']     = 'DEM-CL-' . strtoupper(uniqid());
        $validated['statut']        = 'En attente';

        \App\Models\DemandeIntervention::create($validated);

        return redirect()->route('client.interventions.index')
            ->with('success', 'Votre demande d\'intervention a été soumise avec succès. Votre responsable commercial examinera votre demande et vous contactera par téléphone ou email.');
    }
}
