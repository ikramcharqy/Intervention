<?php

namespace App\Http\Controllers;

use App\Models\ClientContact;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'fonction' => 'nullable|string|max:255',
            'is_principal' => 'boolean',
        ]);

        if ($request->has('is_principal') && $request->is_principal) {
            $client->contacts()->update(['is_principal' => false]);
        }

        $client->contacts()->create($validated);

        return redirect()->route('clients.show', $client)->with('success', 'Contact ajouté avec succès.');
    }

    public function update(Request $request, ClientContact $contact)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:20',
            'fonction' => 'nullable|string|max:255',
            'is_principal' => 'boolean',
        ]);

        if ($request->has('is_principal') && $request->is_principal) {
            $contact->client->contacts()->where('id', '!=', $contact->id)->update(['is_principal' => false]);
        }

        $contact->update($validated);

        return redirect()->route('clients.show', $contact->client_id)->with('success', 'Contact mis à jour.');
    }

    public function destroy(ClientContact $contact)
    {
        $clientId = $contact->client_id;
        $contact->delete();
        return redirect()->route('clients.show', $clientId)->with('success', 'Contact supprimé.');
    }
}
