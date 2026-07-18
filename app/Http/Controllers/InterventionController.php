<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterventionRequest;
use App\Http\Requests\UpdateInterventionRequest;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Emplacement;
use App\Models\Materiau;
use App\Models\TypeIntervention;
use App\Models\User;
use App\Services\InterventionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion des interventions techniques.
 *
 * Actions disponibles :
 *  - CRUD standard          : index, create, store, show, edit, update, destroy
 *  - Workflow statut        : start, suspend, resume, submitValidation, validateIntervention, rejectValidation
 */
class InterventionController extends Controller
{
    protected InterventionService $interventionService;

    public function __construct(InterventionService $interventionService)
    {
        $this->interventionService = $interventionService;
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Standard
    |--------------------------------------------------------------------------
    */

    /**
     * Liste paginée des interventions avec filtres.
     */
    public function index(Request $request): View
    {
        $search   = $request->input('search');
        $statut   = $request->input('statut');
        $priorite = $request->input('priorite');

        $interventions = Intervention::with(['chantier.client', 'technicien', 'typeIntervention'])
            ->when($search, function ($query, $search) {
                $query->where('code_intervention', 'like', "%{$search}%")
                      ->orWhereHas('chantier', function ($q) use ($search) {
                          $q->where('nom', 'like', "%{$search}%")
                            ->orWhereHas('client', fn($qc) => $qc->where('nom', 'like', "%{$search}%"));
                      })
                      ->orWhereHas('technicien', fn($q) =>
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('prenom', 'like', "%{$search}%")
                      );
            })
            ->when($statut,   fn($query, $statut)   => $query->where('statut', $statut))
            ->when($priorite, fn($query, $priorite) => $query->where('priorite', $priorite))
            ->orderBy('date_prevue_debut', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('interventions.index', compact('interventions', 'search', 'statut', 'priorite'));
    }

    /**
     * Formulaire de création d'une intervention.
     */
    public function create(): View
    {
        $clients          = Client::where('is_active', true)->orderBy('nom')->get(['id', 'nom']);
        $chantiers        = Chantier::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'code_chantier']);
        $emplacements     = Emplacement::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'chantier_id']);
        $techniciens      = User::role('technicien')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'prenom']);
        $typesIntervention = TypeIntervention::where('is_active', true)->orderBy('nom')->get(['id', 'nom']);

        return view('interventions.create', compact(
            'clients', 'chantiers', 'emplacements', 'techniciens', 'typesIntervention'
        ));
    }

    /**
     * Enregistre une nouvelle intervention.
     * Le statut est forcé à 'Planifiee' dans le service.
     */
    public function store(StoreInterventionRequest $request): RedirectResponse
    {
        $intervention = $this->interventionService->createIntervention($request->validated());

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "L'intervention **{$intervention->code_intervention}** a été planifiée avec succès.");
    }

    /**
     * Affiche le détail d'une intervention.
     */
    public function show(Intervention $intervention): View
    {
        $intervention->load([
            'chantier.client',
            'emplacement',
            'technicien',
            'typeIntervention',
            'taches.tache',
            'materiaux.materiau',
            'rapport',
            'trackingSessions',
            'historiques.user',
            'createur',
            'validateur',
        ]);

        // Catalogue de matériaux actifs pour le formulaire d'ajout
        $materiaux = Materiau::where('is_active', true)->orderBy('nom')->get();

        return view('interventions.show', compact('intervention', 'materiaux'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Intervention $intervention): View
    {
        $clients          = Client::where('is_active', true)->orderBy('nom')->get(['id', 'nom']);
        $chantiers        = Chantier::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'code_chantier']);
        $emplacements     = Emplacement::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'chantier_id']);
        $techniciens      = User::role('technicien')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'prenom']);
        $typesIntervention = TypeIntervention::where('is_active', true)->orderBy('nom')->get(['id', 'nom']);

        return view('interventions.edit', compact(
            'intervention', 'clients', 'chantiers', 'emplacements', 'techniciens', 'typesIntervention'
        ));
    }

    /**
     * Met à jour une intervention.
     * Les champs statut, chantier_id, technicien_id sont protégés dans le service.
     */
    public function update(UpdateInterventionRequest $request, Intervention $intervention): RedirectResponse
    {
        $this->interventionService->updateIntervention($intervention, $request->validated());

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "L'intervention **{$intervention->code_intervention}** a été mise à jour.");
    }

    /**
     * Annule ou supprime logiquement l'intervention.
     */
    public function destroy(Request $request, Intervention $intervention): RedirectResponse
    {
        $motif = $request->input('motif_annulation', '');

        try {
            $this->interventionService->cancelOrDelete($intervention, $motif);

            return redirect()
                ->route('interventions.index')
                ->with('success', "L'intervention a été annulée avec succès.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Actions de Workflow (Transitions de Statut)
    |--------------------------------------------------------------------------
    */

    /**
     * Le technicien accepte l'intervention planifiée.
     */
    public function accept(Intervention $intervention)
    {
        try {
            $this->interventionService->accept($intervention);

            return back()->with('success', 'Intervention acceptée avec succès.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Démarrage de l'intervention par le technicien.
     * Modes : GPS | QR | NFC | Manuel
     */
    public function start(Request $request, Intervention $intervention): RedirectResponse
    {
        $mode   = $request->input('mode', Intervention::MODE_MANUEL);
        $params = $request->only(['latitude', 'longitude', 'qr_code', 'nfc_tag']);

        try {
            $this->interventionService->startIntervention($intervention, $mode, $params);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été démarrée en mode {$mode}.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Mise en pause de l'intervention par le technicien ou l'admin.
     */
    public function suspend(Request $request, Intervention $intervention): RedirectResponse
    {
        $motif = $request->input('motif', '');

        try {
            $this->interventionService->suspendreIntervention($intervention, $motif);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été mise en pause.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reprise d'une intervention suspendue.
     */
    public function resume(Intervention $intervention): RedirectResponse
    {
        try {
            $this->interventionService->reprendreIntervention($intervention);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a repris.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Soumission pour validation par le technicien.
     * L'intervention passe à 'En attente validation'.
     */
    public function submitValidation(Intervention $intervention): RedirectResponse
    {
        try {
            $this->interventionService->submitForValidation($intervention);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été soumise à la validation de l'administrateur.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Validation finale par l'administrateur (statut → Terminee).
     */
    public function validateIntervention(Intervention $intervention): RedirectResponse
    {
        try {
            $this->interventionService->validateIntervention($intervention);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été validée et est officiellement clôturée.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Rejet de la validation par l'administrateur.
     * Renvoie le technicien en mode 'En cours' avec un motif.
     */
    public function rejectValidation(Request $request, Intervention $intervention): RedirectResponse
    {
        $request->validate([
            'motif' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'motif.required' => 'Un motif de rejet est obligatoire.',
            'motif.min'      => 'Le motif doit contenir au moins :min caractères.',
        ]);

        try {
            $this->interventionService->rejeterValidation($intervention, $request->input('motif'));

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "La validation a été rejetée. Le technicien doit corriger l'intervention.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }
}
