<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChantierRequest;
use App\Http\Requests\UpdateChantierRequest;
use App\Models\Chantier;
use App\Models\Client;
use App\Services\ChantierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion des chantiers de la plateforme.
 */
class ChantierController extends Controller
{
    protected ChantierService $chantierService;

    public function __construct(ChantierService $chantierService)
    {
        $this->chantierService = $chantierService;
    }

    /**
     * Affiche la liste paginée des chantiers.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $chantiers = Chantier::with('client')
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('code_chantier', 'like', "%{$search}%")
                      ->orWhere('ville', 'like', "%{$search}%")
                      ->orWhereHas('client', fn ($q) => $q->where('nom', 'like', "%{$search}%"));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return $this->roleView('chantiers.index', compact('chantiers', 'search'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $clients = Client::where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'code_client']);

        return $this->roleView('chantiers.create', compact('clients'));
    }

    /**
     * Enregistre un chantier.
     */
    public function store(StoreChantierRequest $request): RedirectResponse
    {
        $chantier = $this->chantierService->createChantier($request->validated());

        return redirect()
            ->route('chantiers.show', $chantier)
            ->with('success', "Le chantier **{$chantier->nom}** a été créé avec succès.");
    }

    /**
     * Détails du chantier.
     */
    public function show(Chantier $chantier): View
    {
        $chantier->load([
            'client',
            'emplacements',
            'interventions' => fn ($q) => $q->orderBy('date_prevue_debut', 'desc'),
            'interventions.typeIntervention',
            'interventions.technicien',
        ]);

        return $this->roleView('chantiers.show', compact('chantier'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Chantier $chantier): View
    {
        $clients = Client::where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'code_client']);

        return $this->roleView('chantiers.edit', compact('chantier', 'clients'));
    }

    /**
     * Met à jour un chantier.
     */
    public function update(UpdateChantierRequest $request, Chantier $chantier): RedirectResponse
    {
        $this->chantierService->updateChantier($chantier, $request->validated());

        return redirect()
            ->route('chantiers.show', $chantier)
            ->with('success', "Le chantier **{$chantier->nom}** a été mis à jour avec succès.");
    }

    /**
     * Désactive logiquement.
     */
    public function destroy(Chantier $chantier): RedirectResponse
    {
        $this->chantierService->deactivate($chantier);

        return redirect()
            ->route('chantiers.index')
            ->with('success', "Le chantier **{$chantier->nom}** a été désactivé avec succès.");
    }

    /**
     * Réactive.
     */
    public function restore(Chantier $chantier): RedirectResponse
    {
        $this->chantierService->activate($chantier);

        return redirect()
            ->route('chantiers.show', $chantier)
            ->with('success', "Le chantier **{$chantier->nom}** a été réactivé avec succès.");
    }

    /**
     * Retourne les emplacements actifs d'un chantier en JSON (pour chargement dynamique).
     */
    public function getEmplacements(Chantier $chantier)
    {
        $emplacements = $chantier->emplacements()
            ->where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'qr_code']);

        return response()->json($emplacements);
    }
}
