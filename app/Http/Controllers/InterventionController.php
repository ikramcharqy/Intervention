<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterventionRequest;
use App\Http\Requests\UpdateInterventionRequest;
use App\Models\Intervention;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Emplacement;
use App\Models\TypeIntervention;
use App\Models\User;
use App\Models\GpsTrackingSession;
use App\Models\GpsTrackingPoint;
use App\Services\InterventionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Gestion des interventions techniques.
 */
class InterventionController extends Controller
{
    protected InterventionService $interventionService;

    public function __construct(InterventionService $interventionService)
    {
        $this->interventionService = $interventionService;
    }

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

    public function store(StoreInterventionRequest $request): RedirectResponse
    {
        $intervention = $this->interventionService->createIntervention($request->validated());

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "L'intervention **{$intervention->code_intervention}** a été planifiée avec succès.");
    }

    public function show(Intervention $intervention): View
    {
        $intervention->load([
            'chantier.client',
            'emplacement',
            'technicien',
            'typeIntervention',
            'taches.tache',
            'materiaux.materiau',
            'rapport.reponses.question.choix',
            'rapport.reponses.choixQuestion',
            'rapport.photos',
            'rapport.videos',
            'rapport.documents',
            'trackingSessions',
            'gpsTrackingSessions.points',
            'historiques.user',
            'createur',
            'validateur',
            'interventionParente',
            'interventionsEnfants',
        ]);

        // Formulaire dynamique associé
        $formulaire = \App\Models\Formulaire::with(['questions.choix'])
            ->where('type_intervention_id', $intervention->type_intervention_id)
            ->where('is_active', true)
            ->first();

        $materiaux = \App\Models\Materiau::where('is_active', true)->orderBy('nom')->get();
        $taches = \App\Models\Tache::where('is_active', true)->orderBy('nom')->get();

        return view('interventions.show', compact('intervention', 'formulaire', 'materiaux', 'taches'));
    }

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

    public function update(UpdateInterventionRequest $request, Intervention $intervention): RedirectResponse
    {
        $this->interventionService->updateIntervention($intervention, $request->validated());

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "L'intervention **{$intervention->code_intervention}** a été mise à jour.");
    }

    /**
     * Admin — Liste des interventions créées en attente de planification/affectation.
     */
    public function aPlanifier(Request $request): View
    {
        $search   = $request->input('search');
        $priorite = $request->input('priorite');

        $interventions = Intervention::with(['chantier.client', 'typeIntervention', 'createur'])
            ->whereIn('statut', [Intervention::STATUT_PLANIFIEE, Intervention::STATUT_DEMANDE])
            ->whereNull('technicien_id')
            ->when($search, function ($q, $search) {
                $q->where('code_intervention', 'like', "%{$search}%")
                  ->orWhereHas('chantier', function ($qc) use ($search) {
                      $qc->where('nom', 'like', "%{$search}%")
                         ->orWhereHas('client', fn($qcc) => $qcc->where('nom', 'like', "%{$search}%"));
                  });
            })
            ->when($priorite, fn($q, $p) => $q->where('priorite', $p))
            ->orderByRaw("FIELD(priorite, 'Urgente', 'Haute', 'Normale', 'Faible')")
            ->orderBy('created_at', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('interventions.a_planifier', compact('interventions', 'search', 'priorite'));
    }

    /**
     * Admin — Formulaire de planification et d'affectation d'une intervention.
     */
    public function planifier(Intervention $intervention): View
    {
        $techniciens = User::role('Technicien')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'prenom']);

        $intervention->load(['chantier.client', 'typeIntervention', 'createur']);

        return view('interventions.planifier', compact('intervention', 'techniciens'));
    }

    /**
     * Admin — Sauvegarde la planification (date + technicien) et passe le statut à Affectée.
     */
    public function savePlanification(Request $request, Intervention $intervention): RedirectResponse
    {
        $data = $request->validate([
            'technicien_id'     => 'required|exists:users,id',
            'date_prevue_debut' => 'required|date',
            'date_prevue_fin'   => 'nullable|date|after_or_equal:date_prevue_debut',
            'priorite'          => 'nullable|in:Faible,Normale,Haute,Urgente',
            'commentaire'       => 'nullable|string|max:500',
        ]);

        try {
            $this->interventionService->planifierEtAffecter(
                $intervention,
                (int) $data['technicien_id'],
                $data['date_prevue_debut'],
                $data['date_prevue_fin'] ?? null,
                $data['priorite'] ?? null,
                $data['commentaire'] ?? null
            );

            return redirect()
                ->route('interventions.a-planifier')
                ->with('success', "L'intervention {$intervention->code_intervention} a été planifiée et affectée avec succès.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


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

    public function accept(Intervention $intervention)
    {
        try {
            $this->interventionService->accept($intervention);

            return back()->with('success', 'Intervention acceptée avec succès.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

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

    public function suspend(Request $request, Intervention $intervention): RedirectResponse
    {
        $motif = $request->input('motif', $request->input('motif_suspension', ''));

        try {
            $this->interventionService->suspendreIntervention($intervention, $motif);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été suspendue.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    public function resume(Intervention $intervention): RedirectResponse
    {
        try {
            $this->interventionService->reprendreIntervention($intervention);

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a repris avec succès.");
        } catch (Exception $e) {
            return redirect()
                ->route('interventions.show', $intervention)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Action : Marquer le client absent.
     */
    public function marquerClientAbsent(Request $request, Intervention $intervention): RedirectResponse
    {
        $request->validate([
            'motif'          => 'required|string|max:1000',
            'date_revisite'  => 'nullable|date|after:now',
            'technicien_id'  => 'nullable|exists:users,id',
        ]);

        try {
            $this->interventionService->marquerClientAbsent(
                $intervention,
                $request->input('motif'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été enregistrée avec le statut 'Client absent'.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Action : Marquer matériel manquant.
     */
    public function marquerMaterielManquant(Request $request, Intervention $intervention): RedirectResponse
    {
        $request->validate([
            'motif'          => 'required|string|max:1000',
            'date_revisite'  => 'nullable|date|after:now',
            'technicien_id'  => 'nullable|exists:users,id',
        ]);

        try {
            $this->interventionService->marquerMaterielManquant(
                $intervention,
                $request->input('motif'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été enregistrée avec le statut 'Matériel manquant'.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Action : Marquer intervention partiellement réalisée.
     */
    public function marquerPartiellementRealisee(Request $request, Intervention $intervention): RedirectResponse
    {
        $request->validate([
            'observations'   => 'required|string|max:1000',
            'date_revisite'  => 'nullable|date|after:now',
            'technicien_id'  => 'nullable|exists:users,id',
        ]);

        try {
            $this->interventionService->marquerPartiellementRealisee(
                $intervention,
                $request->input('observations'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return redirect()
                ->route('interventions.show', $intervention)
                ->with('success', "L'intervention a été marquée comme 'Partiellement réalisée'.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Action : Créer une 2ème visite / revisite liée.
     */
    public function creerDeuxiemeVisite(Request $request, Intervention $intervention): RedirectResponse
    {
        $request->validate([
            'date_prevue_debut' => 'required|date',
            'technicien_id'     => 'nullable|exists:users,id',
            'motif'             => 'nullable|string|max:500',
        ]);

        try {
            $revisite = $this->interventionService->creerDeuxiemeVisite($intervention, [
                'date_prevue_debut' => $request->input('date_prevue_debut'),
                'technicien_id'     => $request->input('technicien_id') ? (int) $request->input('technicien_id') : null,
                'motif'             => $request->input('motif', 'Deuxième visite programmée'),
            ]);

            return redirect()
                ->route('interventions.show', $revisite)
                ->with('success', "La deuxième visite (#{$revisite->code_intervention}) a été créée avec succès et liée à l'intervention d'origine.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

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

    public function validateIntervention(Request $request, Intervention $intervention): JsonResponse
    {
        if (!$request->user()->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin'])) {
            return response()->json([
                'message' => 'Non autorisé à valider cette intervention.',
            ], 403);
        }

        try {
            $this->interventionService->validateIntervention($intervention);

            return response()->json([
                'data' => $intervention->fresh(),
                'message' => 'Intervention validée et clôturée avec succès.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
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

    /**
     * Planifie instantanément une intervention géolocalisée basée sur les coordonnées GPS fournies/capturées.
     */
    public function quickSeedLiveGps(Request $request): RedirectResponse
    {
        $lat = (float) $request->input('latitude', 33.5731);
        $lng = (float) $request->input('longitude', -7.5898);

        $chantier = Chantier::first();
        if (!$chantier) {
            $client = Client::firstOrCreate(['nom' => 'Client GPS Live'], [
                'code_client' => 'CL-GPS-01',
                'type_client' => 'Entreprise',
                'telephone'   => '0600000000',
                'is_active'   => true,
            ]);
            $chantier = Chantier::create([
                'client_id'     => $client->id,
                'code_chantier' => 'CH-GPS-01',
                'nom'           => 'Site Principal GPS Live',
                'is_active'     => true,
            ]);
        }

        $emplacement = Emplacement::where('chantier_id', $chantier->id)->first();
        if (!$emplacement) {
            $emplacement = Emplacement::create([
                'chantier_id' => $chantier->id,
                'nom'         => 'Zone GPS Principal',
                'is_active'   => true,
            ]);
        }

        $technicien = User::role('technicien')->first() ?? auth()->user();
        $type = TypeIntervention::firstOrCreate(['nom' => 'Maintenance GPS Live'], ['is_active' => true]);

        $code = 'INT-GPS-' . rand(1000, 9999);

        $intervention = Intervention::create([
            'code_intervention'    => $code,
            'chantier_id'          => $chantier->id,
            'emplacement_id'       => $emplacement->id,
            'technicien_id'        => $technicien->id,
            'type_intervention_id' => $type->id,
            'createur_id'          => auth()->id(),
            'statut'               => 'En cours',
            'priorite'             => 'Urgente',
            'date_prevue_debut'    => now(),
            'date_prevue_fin'      => now()->addHours(2),
            'date_debut_reelle'    => now(),
            'notes_admin'          => "Intervention générée en direct depuis la position GPS réelle (" . round($lat, 5) . ", " . round($lng, 5) . ").",
        ]);

        $session = GpsTrackingSession::create([
            'intervention_id' => $intervention->id,
            'technicien_id'   => $technicien->id,
            'started_at'      => now()->subMinutes(15),
            'distance_metres' => 1850.00,
        ]);

        $waypoints = [
            ['lat' => $lat - 0.0080, 'lng' => $lng - 0.0060, 'mins' => 15],
            ['lat' => $lat - 0.0050, 'lng' => $lng - 0.0040, 'mins' => 10],
            ['lat' => $lat - 0.0020, 'lng' => $lng - 0.0015, 'mins' => 5],
            ['lat' => $lat - 0.0008, 'lng' => $lng - 0.0005, 'mins' => 2],
            ['lat' => $lat,          'lng' => $lng,          'mins' => 0],
        ];

        foreach ($waypoints as $wp) {
            GpsTrackingPoint::create([
                'gps_tracking_session_id' => $session->id,
                'latitude'                => $wp['lat'],
                'longitude'               => $wp['lng'],
                'captured_at'             => now()->subMinutes($wp['mins']),
            ]);
        }

        return redirect()
            ->route('gps.index')
            ->with('success', "Intervention GPS Live #{$intervention->code_intervention} planifiée avec succès depuis votre position actuelle !");
    }

    /**
     * Refuser une intervention et émettre une demande de réaffectation.
     */
    public function refuse(Request $request, Intervention $intervention)
    {
        $request->validate([
            'motif' => 'required|string|min:5',
        ], [
            'motif.required' => 'Le motif de refus est obligatoire pour transmettre la demande de réaffectation.',
        ]);

        try {
            $this->interventionService->refuserEtDemanderReaffectation(
                $intervention,
                $request->input('motif'),
                $request->user()
            );

            return redirect()->back()->with('success', 'Votre demande de réaffectation a été transmise à l\'administrateur.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reporter l'intervention à une date ultérieure.
     */
    public function reschedule(Request $request, Intervention $intervention)
    {
        $request->validate([
            'motif'              => 'required|string|min:5',
            'date_prevue_debut' => 'nullable|date',
            'date_prevue_fin'   => 'nullable|date|after_or_equal:date_prevue_debut',
        ]);

        try {
            $this->interventionService->reporter(
                $intervention,
                $request->input('motif'),
                $request->input('date_prevue_debut'),
                $request->input('date_prevue_fin')
            );

            return redirect()->back()->with('success', 'L\'intervention a été reportée avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rouvrir une intervention terminée ou validée.
     */
    public function reopen(Request $request, Intervention $intervention)
    {
        $request->validate([
            'motif' => 'nullable|string',
        ]);

        try {
            $this->interventionService->rouvrir($intervention, $request->input('motif', ''));
            return redirect()->back()->with('success', 'L\'intervention a été rouverte avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mettre à jour l'avancement/progression d'une tâche d'intervention.
     */
    public function updateTacheProgress(Request $request, Intervention $intervention, int $tachePivotId)
    {
        $request->validate([
            'pourcentage' => 'required|integer|min:0|max:100',
            'statut'      => 'nullable|string',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        try {
            $this->interventionService->updateTacheProgress(
                $intervention,
                $tachePivotId,
                (int) $request->input('pourcentage'),
                $request->input('statut'),
                $request->input('commentaire')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Progression de la tâche enregistrée. Pourcentage global recalculé.',
                    'pourcentage_global' => $intervention->fresh()->pourcentage_global,
                ]);
            }

            return redirect()->back()->with('success', 'Progression de la tâche enregistrée avec succès.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Démarrer une tâche d'intervention.
     */
    public function startTache(Request $request, Intervention $intervention, int $tachePivotId)
    {
        try {
            $this->interventionService->demarrerTache(
                $intervention,
                $tachePivotId,
                $request->input('commentaire')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tâche démarrée.',
                    'pourcentage_global' => $intervention->fresh()->pourcentage_global,
                ]);
            }

            return redirect()->back()->with('success', 'Tâche démarrée.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Terminer une tâche d'intervention.
     */
    public function finishTache(Request $request, Intervention $intervention, int $tachePivotId)
    {
        try {
            $this->interventionService->terminerTache(
                $intervention,
                $tachePivotId,
                $request->input('commentaire')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tâche marquée comme terminée.',
                    'pourcentage_global' => $intervention->fresh()->pourcentage_global,
                ]);
            }

            return redirect()->back()->with('success', 'Tâche marquée comme terminée.');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
