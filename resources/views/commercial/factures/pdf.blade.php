@php
    $documentTitle = 'Facture';
    $documentReference = $facture->reference;
@endphp
@extends('pdf.layout')

@push('styles')
    .recipient-block { font-size: 10px; font-weight: bold; color: #1e293b; line-height: 1.6; }
    .totals-table { width: 280px; float: right; margin-top: 6px; margin-bottom: 16px; border-collapse: collapse; }
    .totals-table td { padding: 7px 12px; font-size: 10.5px; border: 1px solid #e2e8f0; }
    .totals-table tr.grand-total td { background-color: #f1f5f9; font-weight: bold; font-size: 11.5px; }
    .totals-table tr.solde td { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
    .clearfix { clear: both; }
@endpush

@section('meta')
    <strong>Facturé à :</strong><br>
    <div class="recipient-block">
        @if($facture->client)
            {{ $facture->client->nom_contact ?? $facture->client->nom }}<br>
            {{ $facture->client->nom }}
        @else
            Non spécifié
        @endif
    </div>
    @if($facture->devis)<br><strong>Devis d'origine :</strong> {{ $facture->devis->reference }}@endif
@endsection

@section('content')
    <table class="data-table" style="border:none; margin-bottom:16px;">
        <tr>
            <td style="border:none; width:33%;"><strong>Date d'émission</strong><br>{{ $facture->date_emission ? $facture->date_emission->format('d/m/Y') : 'N/A' }}</td>
            <td style="border:none; width:33%;"><strong>Date d'échéance</strong><br>{{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : 'N/A' }}</td>
            <td style="border:none; width:34%;"><strong>Statut</strong><br>{{ $facture->statut }}</td>
        </tr>
    </table>

    @if($facture->devis)
        <div class="section">
            <div class="section-header">Détail des prestations (devis {{ $facture->devis->reference }})</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Désignation</th>
                        <th style="width: 15%; text-align:center;">Quantité</th>
                        <th style="width: 20%; text-align:right;">Prix U. HT</th>
                        <th style="width: 25%; text-align:right;">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facture->devis->lignes as $ligne)
                        <tr>
                            <td>{{ $ligne->designation }}</td>
                            <td style="text-align:center;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                            <td style="text-align:right;">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} {{ $currency }}</td>
                            <td style="text-align:right;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <table class="totals-table">
        <tr>
            <td>Total HT</td>
            <td style="text-align:right;">{{ number_format($facture->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
        <tr>
            <td>TVA ({{ $facture->taux_tva }} %)</td>
            <td style="text-align:right;">{{ number_format($facture->montant_tva, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total TTC</td>
            <td style="text-align:right;">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
        <tr class="solde">
            <td>Solde restant dû</td>
            <td style="text-align:right;">{{ number_format($facture->soldeRestant(), 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
    </table>

    <div class="clearfix"></div>

    @if($facture->observations)
        <div class="section">
            <div class="section-header">Observations</div>
            <div class="info-box" style="font-size:9.5px; white-space:pre-wrap;">{{ $facture->observations }}</div>
        </div>
    @endif
@endsection
