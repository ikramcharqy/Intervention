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
        
        if (auth()->user()->hasRole('Commercial')) {
            $query->where('commercial_id', auth()->id());
        }

        $demandes = $query->latest()->get();
        return $this->roleView('demande_interventions.index', compact('demandes'));
    }

    public function create()
    {
        $clients = auth()->user()->hasRole('Commercial') 
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
        $clients = auth()->user()->hasRole('Commercial') 
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

    /**
     * Le Commercial valide la demande d'intervention après échange (téléphone / email),
     * et génère l'Intervention pour l'Administration (statut Planifiee).
     */
    public function validerEtConvertir(Request $request, DemandeIntervention $demandeIntervention)
    {
        $request->validate([
            'compte_rendu_echange' => 'nullable|string',
            'date_prevue_souhaitee' => 'nullable|date',
            'notes_commercial'     => 'nullable|string',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $demandeIntervention) {
            $compteRendu = $request->input('compte_rendu_echange', '');
            $notes = $request->input('notes_commercial', '');

            // 1. Mise à jour de la demande
            $demandeIntervention->update([
                'statut'      => 'Acceptee',
                'description' => trim(($demandeIntervention->description ?? '') . "\n\n[QUALIFICATION COMMERCIAL (" . now()->format('d/m/Y H:i') . ")]\nEchange client: " . ($compteRendu ?: 'Contacté par téléphone/email') . "\nNotes: " . $notes),
            ]);

            // 2. Récupération / Création de l'emplacement du chantier
            $emplacement = \App\Models\Emplacement::where('chantier_id', $demandeIntervention->chantier_id)->first();
            if (!$emplacement && $demandeIntervention->chantier_id) {
                $emplacement = \App\Models\Emplacement::create([
                    'chantier_id' => $demandeIntervention->chantier_id,
                    'nom'         => 'Emplacement Principal',
                    'is_active'   => true,
                ]);
            }

            // 3. Génération du code intervention unique
            $codeIntervention = 'INT-' . strtoupper(uniqid());

            // 4. Création de l'Intervention transmise à l'Admin
            $intervention = \App\Models\Intervention::create([
                'code_intervention'    => $codeIntervention,
                'chantier_id'          => $demandeIntervention->chantier_id,
                'emplacement_id'       => $emplacement?->id,
                'technicien_id'        => null, // À affecter par l'Admin
                'type_intervention_id' => $demandeIntervention->type_intervention_id,
                'cree_par'             => auth()->id(),
                'statut'               => \App\Models\Intervention::STATUT_PLANIFIEE,
                'priorite'             => $demandeIntervention->priorite ?? \App\Models\Intervention::PRIORITE_NORMALE,
                'date_prevue_debut'    => $request->input('date_prevue_souhaitee') ?? now()->addDay(),
                'date_prevue_fin'      => $request->input('date_prevue_souhaitee') ? \Carbon\Carbon::parse($request->input('date_prevue_souhaitee'))->addHours(3) : now()->addDay()->addHours(3),
                'description'          => "[Demandé par le client via " . ($demandeIntervention->reference ?? 'Demande') . "]\nObjet: " . $demandeIntervention->objet . "\nDetails: " . $demandeIntervention->description,
            ]);

            // Historique de création
            \App\Models\InterventionHistorique::create([
                'intervention_id' => $intervention->id,
                'user_id'         => auth()->id(),
                'statut_avant'    => '—',
                'statut_apres'    => \App\Models\Intervention::STATUT_PLANIFIEE,
                'commentaire'     => "Intervention créée suite à la qualification commerciale de la demande #{$demandeIntervention->reference}.",
            ]);

            return redirect()->route('demande-interventions.index')
                ->with('success', "Demande qualifiée et validée ! L'intervention #{$intervention->code_intervention} a été transmise à l'administration pour planification et affectation.");
        });
    }

    /**
     * Le Commercial refuse la demande d'intervention client avec motif explicatif.
     */
    public function refuser(Request $request, DemandeIntervention $demandeIntervention)
    {
        $request->validate([
            'motif_refus' => 'required|string|min:5',
        ], [
            'motif_refus.required' => 'Le motif du refus est obligatoire pour informer le client.',
        ]);

        $motif = $request->input('motif_refus');

        $demandeIntervention->update([
            'statut'      => 'Refusee',
            'description' => trim(($demandeIntervention->description ?? '') . "\n\n[REFUS COMMERCIAL (" . now()->format('d/m/Y H:i') . ")]\nMotif: " . $motif),
        ]);

        return redirect()->route('demande-interventions.index')
            ->with('success', 'La demande d\'intervention a été refusée avec succès.');
    }
}
