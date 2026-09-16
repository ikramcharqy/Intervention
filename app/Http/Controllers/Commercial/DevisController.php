<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Prospect;
use App\Models\Client;
use App\Models\DemandeIntervention;
use App\Http\Requests\StoreDevisRequest;
use App\Models\Setting;
use App\Services\DevisService;
use App\Services\DemandeInterventionService;
use App\Services\FactureService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevisController extends Controller
{
    public function __construct(
        private DevisService $devisService,
        private DemandeInterventionService $demandeInterventionService,
        private FactureService $factureService,
    ) {
    }

    public function index(Request $request): View
    {
        $commercialId = auth()->id();
        $query = Devis::with(['prospect', 'client'])
            ->where('commercial_id', $commercialId);

        // Relance : devis Envoyé/En attente depuis plus de X jours sans réponse.
        if ($request->boolean('relance')) {
            $query->whereIn('statut', ['Envoyé', 'En attente'])
                ->where('date_emission', '<=', now()->subDays(DashboardController::DEVIS_RELANCE_JOURS));
        }

        $devis = $query->latest()->get();
        $currency = currency_symbol();

        return view('commercial.devis.index', compact('devis', 'currency'));
    }

    public function create(Request $request): View
    {
        $commercialId = auth()->id();
        $prospects = Prospect::where('commercial_id', $commercialId)->where('statut', '!=', 'Converti')->get();
        $clients = Client::where('commercial_id', $commercialId)->get();
        $demandesInterventions = DemandeIntervention::where('commercial_id', $commercialId)->latest()->get();

        $selectedProspectId = $request->query('prospect_id');

        // Besoin(s) technique(s) identifiés sur la fiche prospect, pour pré-remplir
        // la désignation de la première ligne du devis.
        $besoinsProspect = $selectedProspectId
            ? optional(Prospect::find($selectedProspectId))->typesIntervention->pluck('nom')->implode(', ')
            : null;

        return view('commercial.devis.create', compact('prospects', 'clients', 'demandesInterventions', 'selectedProspectId', 'besoinsProspect'));
    }

    public function store(StoreDevisRequest $request)
    {
        $this->devisService->create($request->validated());

        return redirect()->route('commercial.devis.index')->with('success', 'Devis créé avec succès.');
    }

    public function show(Devis $devis): View
    {
        $devis->load(['prospect', 'client', 'lignes', 'demandeIntervention.intervention', 'commercial']);
        $company = ['name' => Setting::get('company_name', 'TechInterv Solutions')];
        $currency = currency_symbol();

        return view('commercial.devis.show', compact('devis', 'company', 'currency'));
    }

    public function edit(Devis $devis): View
    {
        $devis->load('lignes');
        $commercialId = auth()->id();
        $prospects = Prospect::where('commercial_id', $commercialId)->get();
        $clients = Client::where('commercial_id', $commercialId)->get();
        $demandesInterventions = DemandeIntervention::where('commercial_id', $commercialId)->latest()->get();

        return view('commercial.devis.edit', compact('devis', 'prospects', 'clients', 'demandesInterventions'));
    }

    public function update(StoreDevisRequest $request, Devis $devis)
    {
        $this->devisService->update($devis, $request->validated());

        return redirect()->route('commercial.devis.show', $devis)->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Devis $devis)
    {
        $devis->delete();
        return redirect()->route('commercial.devis.index')->with('success', 'Devis supprimé avec succès.');
    }

    public function creerDemandeIntervention(Devis $devis)
    {
        abort_if($devis->commercial_id !== auth()->id(), 403);

        try {
            $demande = $this->demandeInterventionService->createFromDevis($devis);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.devis.show', $devis)
            ->with('success', "Demande d'intervention {$demande->reference} créée avec succès.");
    }

    public function genererFacture(Devis $devis)
    {
        abort_if($devis->commercial_id !== auth()->id(), 403);

        try {
            $facture = $this->factureService->createFromDevis($devis);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('commercial.factures.show', $facture)
            ->with('success', "Facture {$facture->reference} générée avec succès.");
    }

    public function duplicate(Devis $devis)
    {
        $newDevis = $this->devisService->duplicate($devis);

        return redirect()->route('commercial.devis.show', $newDevis)->with('success', 'Devis dupliqué avec succès en Brouillon.');
    }

    public function generatePdf(Devis $devis)
    {
        abort_if($devis->commercial_id !== auth()->id(), 403);

        $pdf = $this->devisService->generatePdf($devis);

        return $pdf->stream('devis_' . ($devis->reference ?? $devis->id) . '.pdf');
    }
}
