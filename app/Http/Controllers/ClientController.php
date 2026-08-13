<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion des clients de la plateforme.
 */
class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Affiche la liste paginée des clients avec recherche optionnelle.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $commercialFilter = $request->input('commercial_id');

        // Si l'utilisateur connecté est un Commercial, il ne voit que ses propres clients
        if (auth()->user()->hasRole('Commercial')) {
            $commercialFilter = auth()->id();
        }

        $clients = Client::with('commercial')
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('code_client', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
            })
            ->when($commercialFilter, function ($query, $commercialFilter) {
                $query->where('commercial_id', $commercialFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Liste des commerciaux pour le filtre
        $commerciaux = User::role('Commercial')->orderBy('name')->get();

        return $this->roleView('clients.index', compact('clients', 'search', 'commerciaux', 'commercialFilter'));
    }

    /**
     * Affiche le formulaire de création d'un client.
     */
    public function create(): View
    {
        $commerciaux = User::role('Commercial')->where('is_active', true)->orderBy('name')->get();
        return $this->roleView('clients.create', compact('commerciaux'));
    }

    /**
     * Enregistre un nouveau client en base de données.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-assigner le commercial si l'utilisateur connecté est un Commercial
        if (auth()->user()->hasRole('Commercial') && empty($data['commercial_id'])) {
            $data['commercial_id'] = auth()->id();
        }

        $client = $this->clientService->createClient($data);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', "Le client **{$client->nom}** a été créé avec succès.");
    }

    /**
     * Affiche le détail d'un client.
     */
    public function show(Client $client): View
    {
        $client->load([
            'clientEntreprise',
            'commercial',
            'contacts',
            'chantiers' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);

        return $this->roleView('clients.show', compact('client'));
    }

    /**
     * Affiche le formulaire d'édition d'un client.
     */
    public function edit(Client $client): View
    {
        $client->load('clientEntreprise');
        $commerciaux = User::role('Commercial')->where('is_active', true)->orderBy('name')->get();

        return $this->roleView('clients.edit', compact('client', 'commerciaux'));
    }

    /**
     * Met à jour un client existant.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $this->clientService->updateClient($client, $request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', "Le client **{$client->nom}** a été mis à jour avec succès.");
    }

    /**
     * Désactive logiquement un client.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->clientService->deactivate($client);

        return redirect()
            ->route('clients.index')
            ->with('success', "Le client **{$client->nom}** a été désactivé avec succès.");
    }

    /**
     * Réactive un client précédemment désactivé.
     */
    public function restore(Client $client): RedirectResponse
    {
        $this->clientService->activate($client);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', "Le client **{$client->nom}** a été réactivé avec succès.");
    }

    /**
     * Retourne les chantiers actifs d'un client en JSON (pour chargement dynamique).
     */
    public function getChantiers(Client $client)
    {
        $chantiers = $client->chantiers()
            ->where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'code_chantier']);

        return response()->json($chantiers);
    }

    /**
     * Suppression définitive d'un client (réservé aux admins, workflow de validation).
     */
    public function forceDelete(Client $client): RedirectResponse
    {
        $nom = $client->nom;
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', "Le client **{$nom}** a été supprimé définitivement.");
    }
}
