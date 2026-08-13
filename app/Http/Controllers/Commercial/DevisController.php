<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Devis;
use App\Models\Prospect;
use App\Models\Client;
use App\Http\Requests\StoreDevisRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    public function index(): View
    {
        $commercialId = auth()->id();
        $devis = Devis::with(['prospect', 'client'])
            ->where('commercial_id', $commercialId)
            ->latest()
            ->get();

        return view('commercial.devis.index', compact('devis'));
    }

    public function create(Request $request): View
    {
        $commercialId = auth()->id();
        $prospects = Prospect::where('commercial_id', $commercialId)->where('statut', '!=', 'Converti')->get();
        $clients = Client::where('commercial_id', $commercialId)->get();
        
        $selectedProspectId = $request->query('prospect_id');

        return view('commercial.devis.create', compact('prospects', 'clients', 'selectedProspectId'));
    }

    public function store(StoreDevisRequest $request)
    {
        $validated = $request->validated();
        $validated['commercial_id'] = auth()->id();
        $validated['reference'] = 'DEVIS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $montant_ht = 0;
        foreach ($request->input('lignes') as $ligne) {
            $montant_ht += floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']);
        }

        $validated['montant_ht'] = $montant_ht;
        $validated['montant_tva'] = $montant_ht * (floatval($validated['taux_tva']) / 100);
        $validated['montant_ttc'] = $validated['montant_ht'] + $validated['montant_tva'];

        $devis = Devis::create($validated);

        foreach ($request->input('lignes') as $ligne) {
            $devis->lignes()->create([
                'designation' => $ligne['designation'],
                'description' => $ligne['description'] ?? null,
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'montant_ht' => floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']),
            ]);
        }

        return redirect()->route('commercial.devis.index')->with('success', 'Devis créé avec succès.');
    }

    public function show(Devis $devis): View
    {
        $devis->load(['prospect', 'client', 'lignes']);
        return view('commercial.devis.show', compact('devis'));
    }

    public function edit(Devis $devis): View
    {
        $devis->load('lignes');
        $commercialId = auth()->id();
        $prospects = Prospect::where('commercial_id', $commercialId)->get();
        $clients = Client::where('commercial_id', $commercialId)->get();

        return view('commercial.devis.edit', compact('devis', 'prospects', 'clients'));
    }

    public function update(StoreDevisRequest $request, Devis $devis)
    {
        $validated = $request->validated();

        $montant_ht = 0;
        foreach ($request->input('lignes') as $ligne) {
            $montant_ht += floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']);
        }

        $validated['montant_ht'] = $montant_ht;
        $validated['montant_tva'] = $montant_ht * (floatval($validated['taux_tva']) / 100);
        $validated['montant_ttc'] = $validated['montant_ht'] + $validated['montant_tva'];

        $devis->update($validated);

        $devis->lignes()->delete();
        foreach ($request->input('lignes') as $ligne) {
            $devis->lignes()->create([
                'designation' => $ligne['designation'],
                'description' => $ligne['description'] ?? null,
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'montant_ht' => floatval($ligne['quantite']) * floatval($ligne['prix_unitaire']),
            ]);
        }

        return redirect()->route('commercial.devis.show', $devis)->with('success', 'Devis mis à jour avec succès.');
    }

    public function destroy(Devis $devis)
    {
        $devis->delete();
        return redirect()->route('commercial.devis.index')->with('success', 'Devis supprimé avec succès.');
    }

    public function duplicate(Devis $devis)
    {
        $newDevis = $devis->replicate();
        $newDevis->reference = 'DEVIS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $newDevis->statut = 'Brouillon';
        $newDevis->date_emission = now();
        $newDevis->save();

        foreach ($devis->lignes as $ligne) {
            $newLigne = $ligne->replicate();
            $newLigne->devis_id = $newDevis->id;
            $newLigne->save();
        }

        return redirect()->route('commercial.devis.show', $newDevis)->with('success', 'Devis dupliqué avec succès en Brouillon.');
    }

    public function generatePdf(Devis $devis)
    {
        $devis->load(['prospect', 'client', 'lignes', 'commercial']);
        $pdf = Pdf::loadView('commercial.devis.pdf', compact('devis'));
        return $pdf->stream('devis_' . ($devis->reference ?? $devis->id) . '.pdf');
    }
}
