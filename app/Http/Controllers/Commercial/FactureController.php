<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Services\FactureService;
use Illuminate\View\View;

class FactureController extends Controller
{
    public function __construct(
        private FactureService $factureService,
    ) {
    }

    public function index(): View
    {
        $factures = Facture::with(['client', 'devis'])
            ->where('commercial_id', auth()->id())
            ->latest()
            ->get();

        $currency = currency_symbol();

        return view('commercial.factures.index', compact('factures', 'currency'));
    }

    public function show(Facture $facture): View
    {
        abort_if($facture->commercial_id !== auth()->id(), 403);

        $facture->load(['client', 'devis.lignes', 'commercial']);
        $currency = currency_symbol();

        return view('commercial.factures.show', compact('facture', 'currency'));
    }

    public function marquerPayee(Facture $facture)
    {
        abort_if($facture->commercial_id !== auth()->id(), 403);

        $this->factureService->marquerPayee($facture);

        return redirect()->route('commercial.factures.show', $facture)
            ->with('success', 'Facture marquée comme payée.');
    }

    public function generatePdf(Facture $facture)
    {
        abort_if($facture->commercial_id !== auth()->id(), 403);

        $pdf = $this->factureService->generatePdf($facture);

        return $pdf->stream('facture_' . ($facture->reference ?? $facture->id) . '.pdf');
    }
}
