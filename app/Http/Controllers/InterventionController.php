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
            'rapport',
            'trackingSessions',
            'gpsTrackingSessions.points',
            'historiques.user',
            'createur',
            'validateur',
        ]);

        $materiaux = \App\Models\Materiau::where('is_active', true)->orderBy('nom')->get();
        $taches = \App\Models\Tache::where('is_active', true)->orderBy('nom')->get();

        return view('interventions.show', compact('intervention', 'materiaux', 'taches'));
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
}
