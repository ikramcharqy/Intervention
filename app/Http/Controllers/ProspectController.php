<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Client;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    public function index()
    {
        $query = Prospect::with('commercial')->where('statut', '!=', 'Converti');
        // Si c'est un commercial, il ne voit que ses propres prospects
        if (auth()->user()->hasRole('Commercial')) {
            $query->where('commercial_id', auth()->id());
        }
        $prospects = $query->latest()->get();
        return $this->roleView('prospects.index', compact('prospects'));
    }

    public function create()
    {
        return $this->roleView('prospects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'nom_contact' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'statut' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        if (auth()->user()->hasRole('Commercial')) {
            $validated['commercial_id'] = auth()->id();
        } else {
            $validated['commercial_id'] = $request->input('commercial_id', auth()->id());
        }

        $validated['statut'] = $validated['statut'] ?? 'Nouveau';
        $validated['notes'] = [];
        $validated['historique'] = [
            [
                'date' => now()->toDateTimeString(),
                'user' => auth()->user()->name,
                'action' => 'Création du prospect'
            ]
        ];

        Prospect::create($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospect créé avec succès.');
    }

    public function show(Prospect $prospect)
    {
        return $this->roleView('prospects.show', compact('prospect'));
    }

    public function edit(Prospect $prospect)
    {
        return $this->roleView('prospects.edit', compact('prospect'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'nom_entreprise' => 'required|string|max:255',
            'nom_contact' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
            'statut' => 'required|string',
            'observations' => 'nullable|string',
        ]);

        $oldStatut = $prospect->statut;
        $history = $prospect->historique ?? [];

        if ($oldStatut !== $validated['statut']) {
            $history[] = [
                'date' => now()->toDateTimeString(),
                'user' => auth()->user()->name,
                'action' => "Changement de statut de '$oldStatut' à '{$validated['statut']}'"
            ];
        }

        $validated['historique'] = $history;

        $prospect->update($validated);

        return redirect()->route('prospects.index')->with('success', 'Prospect mis à jour avec succès.');
    }

    public function destroy(Prospect $prospect)
    {
        $prospect->delete();
        return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }

    public function convert(Prospect $prospect)
    {
        $prospect->update(['statut' => 'Converti']);

        // log conversion to history
        $history = $prospect->historique ?? [];
        $history[] = [
            'date' => now()->toDateTimeString(),
            'user' => auth()->user()->name,
            'action' => "Prospect converti en Client"
        ];
        $prospect->update(['historique' => $history]);

        $client = Client::create([
            'code_client' => 'CL-' . strtoupper(uniqid()),
            'type_client' => 'B2B',
            'nom' => $prospect->nom_entreprise,
            'nom_contact' => $prospect->nom_contact,
            'email' => $prospect->email,
            'telephone' => $prospect->telephone,
            'adresse_facturation' => $prospect->adresse,
            'commercial_id' => $prospect->commercial_id,
            'is_active' => true,
        ]);

        \App\Models\AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name,
            'action'     => 'Conversion Prospect → Client',
            'module'     => 'Commercial',
            'severity'   => 'info',
            'ip_address' => request()->ip(),
            'details'    => "Prospect '{$prospect->nom_entreprise}' converti avec succès en Client #{$client->id} ({$client->code_client}).",
        ]);

        return redirect()->route('clients.show', $client)->with('success', 'Prospect converti en client avec succès.');
    }

    public function addNote(Request $request, Prospect $prospect)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $notes = $prospect->notes ?? [];
        $notes[] = [
            'date' => now()->toDateTimeString(),
            'user' => auth()->user()->name,
            'content' => $request->input('content')
        ];

        $history = $prospect->historique ?? [];
        $history[] = [
            'date' => now()->toDateTimeString(),
            'user' => auth()->user()->name,
            'action' => "Ajout d'une note"
        ];

        $prospect->update([
            'notes' => $notes,
            'historique' => $history
        ]);

        return redirect()->back()->with('success', 'Note ajoutée avec succès.');
    }
}
