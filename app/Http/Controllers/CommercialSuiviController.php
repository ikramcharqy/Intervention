<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Prospect;
use Illuminate\View\View;

/**
 * Suivi en lecture seule des actions effectuées par les commerciaux
 * (Prospects & Leads, Devis & Offres) — destiné à l'Admin.
 *
 * Ce contrôleur ne fait que lire : la gestion/édition reste exclusivement
 * dans ProspectController et Commercial\DevisController, réservés au
 * workflow des commerciaux eux-mêmes.
 */
class CommercialSuiviController extends Controller
{
    public function prospects(): View
    {
        $prospects = Prospect::with('commercial')->latest()->get();

        return view('commercial-suivi.prospects', compact('prospects'));
    }

    public function prospectShow(Prospect $prospect): View
    {
        $prospect->load('commercial');

        return view('commercial-suivi.prospect-show', compact('prospect'));
    }

    public function devis(): View
    {
        $devis = Devis::with(['commercial', 'client', 'prospect'])->latest()->get();

        return view('commercial-suivi.devis', compact('devis'));
    }

    public function devisShow(Devis $devis): View
    {
        $devis->load(['commercial', 'client', 'prospect', 'lignes']);

        return view('commercial-suivi.devis-show', compact('devis'));
    }
}
