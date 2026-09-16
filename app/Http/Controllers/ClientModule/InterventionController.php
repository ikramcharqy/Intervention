<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\DemandeIntervention;
use App\Services\DemandeInterventionService;
use App\Services\InterventionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InterventionController extends Controller
{
    public function __construct(
        private InterventionService $interventionService,
        private DemandeInterventionService $demandeInterventionService,
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

        $statutFiltre = $request->input('statut', 'tous');
        $chantierFiltre = $request->input('chantier_id');
        $recherche = $request->input('q');
        $avecRapport = $request->boolean('avec_rapport');

        if (!$client) {
            $interventions = collect();
            $chantiersDuClient = collect();
            return view('client.interventions.index', compact('interventions', 'statutFiltre', 'chantierFiltre', 'recherche', 'chantiersDuClient', 'avecRapport'));
        }

        $chantiersDuClient = Chantier::where('client_id', $client->id)->orderBy('nom')->get(['id', 'nom']);
        $chantierIds = $chantiersDuClient->pluck('id');

        // Même périmètre de base que ClientModule\DashboardController::index() via
        // InterventionService::pourChantiers() : les deux pages restent synchronisées.
        $query = $this->interventionService->pourChantiers($chantierIds)
            ->with(['chantier', 'technicien', 'typeIntervention', 'rapport']);

        // Filtre sur les 4 catégories client (InterventionService::GROUPES_STATUT_CLIENT),
        // qui masquent les ~19 statuts internes du workflow sans y toucher.
        if ($statutFiltre && $statutFiltre !== 'tous') {
            $query->whereIn('statut', $this->interventionService->statutsPourGroupeClient($statutFiltre));
        }

        // Affinage optionnel, combinable avec n'importe quel onglet : une intervention
        // "Terminée" peut légitimement ne pas encore avoir de rapport (cf. commentaire
        // sur InterventionService::GROUPES_STATUT_CLIENT) — ce n'est donc pas un onglet
        // à part mais une case à cocher.
        if ($avecRapport) {
            $query->whereHas('rapport');
        }

        if ($chantierFiltre) {
            $query->where('chantier_id', $chantierFiltre);
        }

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('code_intervention', 'like', "%{$recherche}%")
                    ->orWhereHas('chantier', fn ($c) => $c->where('nom', 'like', "%{$recherche}%"));
            });
        }

        $interventions = $query->orderBy('date_prevue_debut', 'desc')->paginate(15)->withQueryString();

        return view('client.interventions.index', compact('interventions', 'statutFiltre', 'chantierFiltre', 'recherche', 'chantiersDuClient', 'avecRapport'));
    }

    public function show(Intervention $intervention): View
    {
        $client = $this->getClient();

        // Sécurité : vérification que l'intervention appartient au client
        if (!$client || !$intervention->chantier || $intervention->chantier->client_id !== $client->id) {
            abort(403, 'Accès non autorisé à cette intervention.');
        }

        $intervention->load(['chantier', 'emplacement', 'technicien', 'typeIntervention', 'rapport.photos', 'rapport.documents', 'materiaux.materiau', 'taches.tache']);

        return view('client.interventions.show', compact('intervention'));
    }

    /**
     * Formulaire pour permettre au Client d'émettre une demande d'intervention.
     * Le paramètre "depuis" permet de pré-remplir la demande à partir d'une demande
     * refusée (action "Soumettre une nouvelle demande liée" côté Mes Demandes).
     */
    public function createDemande(Request $request): View
    {
        $client = $this->getClient();
        $chantiers = $client ? Chantier::where('client_id', $client->id)->get() : collect();
        $typesIntervention = \App\Models\TypeIntervention::where('is_active', true)->get();

        $demandeOrigine = null;
        if ($client && $request->filled('depuis')) {
            $demandeOrigine = DemandeIntervention::where('id', $request->input('depuis'))
                ->where('client_id', $client->id)
                ->first();
        }

        return view('client.interventions.create_demande', compact('chantiers', 'typesIntervention', 'demandeOrigine'));
    }

    /**
     * Enregistre la demande d'intervention émise par le Client (logique déléguée
     * à DemandeInterventionService::createFromClientPortal, partagée avec le canal Commercial).
     */
    public function storeDemande(Request $request)
    {
        $client = $this->getClient();
        if (!$client) {
            abort(403, 'Profil client introuvable.');
        }

        $validated = $request->validate([
            'chantier_id'          => 'required|exists:chantiers,id',
            'type_intervention_id' => 'required|exists:type_interventions,id',
            'priorite'             => 'required|string',
            'objet'                => 'required|string|max:255',
            'description'          => 'required|string',
            'creneau_souhaite'     => 'nullable|string|max:255',
            'contact_sur_site'     => 'nullable|string|max:255',
            'photos.*'             => 'nullable|image|max:5120',
        ]);

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photos[] = $file->store('demandes/photos', 'public');
            }
        }

        $this->demandeInterventionService->createFromClientPortal($client, $validated, $photos);

        return redirect()->route('client.demandes.index')
            ->with('success', 'Votre demande d\'intervention a été soumise avec succès. Votre responsable commercial examinera votre demande et vous contactera par téléphone ou email.');
    }
}
