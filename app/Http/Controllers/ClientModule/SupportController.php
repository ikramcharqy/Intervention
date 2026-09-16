<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TicketSupport;
use App\Services\TicketSupportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct(
        private TicketSupportService $ticketSupportService,
    ) {
    }

    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    public function index(): View
    {
        $client = $this->getClient();
        $commercial = $client?->commercial;

        $tickets = $client
            ? TicketSupport::where('client_id', $client->id)->latest()->paginate(10)
            : collect();

        return view('client.support.index', compact('commercial', 'tickets'));
    }

    public function store(Request $request)
    {
        $client = $this->getClient();
        if (!$client) {
            abort(403, 'Profil client introuvable.');
        }

        $validated = $request->validate([
            'objet' => 'required|string|max:255',
            'message' => 'required|string',
            'categorie' => 'nullable|in:' . implode(',', TicketSupport::CATEGORIES),
            'piece_jointe' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('piece_jointe')) {
            $validated['piece_jointe'] = $request->file('piece_jointe')->store('support/pieces-jointes', 'public');
        }

        $ticket = $this->ticketSupportService->creer($client, auth()->user(), $validated);

        return redirect()->route('client.support.index')
            ->with('success', "Votre message a été envoyé — référence #{$ticket->reference}. Votre responsable commercial vous répondra dans les meilleurs délais.");
    }
}
