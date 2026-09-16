<?php

namespace App\Http\Controllers\ClientModule;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Devis;
use App\Models\Facture;
use Illuminate\View\View;

class FactureController extends Controller
{
    private function getClient(): ?Client
    {
        $user = auth()->user();
        if ($user->client_id) {
            return Client::find($user->client_id);
        }
        return Client::where('email', $user->email)->first();
    }

    /**
     * Vue "Facturation & Devis" : liste combinée, séparée par sous-catégorie
     * comme demandé (le client ne gère pas de sous-menu séparé, un filtre suffit ici).
     */
    public function index(): View
    {
        $client = $this->getClient();

        $devis = $client
            ? Devis::where('client_id', $client->id)->latest()->get()
            : collect();

        $factures = $client
            ? Facture::where('client_id', $client->id)->latest()->get()
            : collect();

        $currency = currency_symbol();

        return view('client.factures.index', compact('devis', 'factures', 'currency'));
    }

    public function pdf(Facture $facture)
    {
        $client = $this->getClient();
        abort_if(!$client || $facture->client_id !== $client->id, 403);

        return app(\App\Services\FactureService::class)->generatePdf($facture)
            ->stream('facture_' . ($facture->reference ?? $facture->id) . '.pdf');
    }
}
