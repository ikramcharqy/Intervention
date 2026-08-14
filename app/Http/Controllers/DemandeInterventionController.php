<?php

namespace App\Http\Controllers;

use App\Models\DemandeIntervention;
use App\Models\Client;
use App\Models\TypeIntervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DemandeInterventionController extends Controller
{
    public function index()
    {
        $query = DemandeIntervention::with(['commercial', 'client', 'chantier', 'typeIntervention']);
        
        if (auth()->user()->hasAnyRole(config('roles.COMMERCIAL'))) {
            $query->where('commercial_id', auth()->id());
        }

        $demandes = $query->latest()->get();
        return $this->roleView('demande_interventions.index', compact('demandes'));
    }

    public function create()
    {
        $clients = auth()->user()->hasAnyRole(config('roles.COMMERCIAL')) 
            ? Client::where('commercial_id', auth()->id())->get()
            : Client::all();
            
        $typesIntervention = TypeIntervention::where('is_active', true)->get();
        
        return $this->roleView('demande_interventions.create', compact('clients', 'typesIntervention'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'type_intervention_id' => 'nullable|exists:type_interventions,id',
            'priorite' => 'required|string',
            'objet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'nullable|image|max:5120',
            'documents.*' => 'nullable|file|max:10240',
        ]);

        $validated['commercial_id'] = auth()->id();
        $validated['reference'] = 'DEM-' . strtoupper(uniqid());

        // Handle uploads
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photos[] = $file->store('demandes/photos', 'public');
            }
        }
        $validated['photos'] = empty($photos) ? null : $photos;

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documents[] = $file->store('demandes/documents', 'public');
            }
        }
        $validated['documents'] = empty($documents) ? null : $documents;

        DemandeIntervention::create($validated);

        return redirect()->route('demande-interventions.index')->with('success', 'Demande d\'intervention créée avec succès.');
    }

    public function show(DemandeIntervention $demandeIntervention)
    {
        return $this->roleView('demande_interventions.show', compact('demandeIntervention'));
    }

    public function edit(DemandeIntervention $demandeIntervention)
    {
        $clients = auth()->user()->hasAnyRole(config('roles.COMMERCIAL')) 
            ? Client::where('commercial_id', auth()->id())->get()
            : Client::all();
            
        $typesIntervention = TypeIntervention::where('is_active', true)->get();
        
        return $this->roleView('demande_interventions.edit', compact('demandeIntervention', 'clients', 'typesIntervention'));
    }

    public function update(Request $request, DemandeIntervention $demandeIntervention)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'type_intervention_id' => 'nullable|exists:type_interventions,id',
            'priorite' => 'required|string',
            'objet' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'nullable|image|max:5120',
            'documents.*' => 'nullable|file|max:10240',
        ]);

        // Append new uploads
        $photos = is_array($demandeIntervention->photos) ? $demandeIntervention->photos : [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $photos[] = $file->store('demandes/photos', 'public');
            }
        }
        $validated['photos'] = empty($photos) ? null : $photos;

        $documents = is_array($demandeIntervention->documents) ? $demandeIntervention->documents : [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $documents[] = $file->store('demandes/documents', 'public');
            }
        }
        $validated['documents'] = empty($documents) ? null : $documents;

        $demandeIntervention->update($validated);

        return redirect()->route('demande-interventions.show', $demandeIntervention)->with('success', 'Demande d\'intervention mise à jour.');
    }

    public function destroy(DemandeIntervention $demandeIntervention)
    {
        $demandeIntervention->delete();
        return redirect()->route('demande-interventions.index')->with('success', 'Demande supprimée.');
    }
}
