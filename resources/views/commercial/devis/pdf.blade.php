@php
    $documentTitle = 'Devis';
    $documentReference = $devis->reference ?? 'DEVIS-'.$devis->id;
@endphp
@extends('pdf.layout')

@push('styles')
    .recipient-block { font-size: 10px; font-weight: bold; color: #1e293b; line-height: 1.6; }
    .expiration-note { display: inline-block; font-size: 9.5px; font-weight: bold; padding: 3px 9px; border-radius: 10px; margin-bottom: 16px; }
    .totals-table { width: 280px; float: right; margin-top: 6px; margin-bottom: 16px; border-collapse: collapse; }
    .totals-table td { padding: 7px 12px; font-size: 10.5px; border: 1px solid #e2e8f0; }
    .totals-table tr.grand-total td { background-color: #ecfdf5; color: #047857; font-weight: bold; font-size: 11.5px; }
    .clearfix { clear: both; }
    .terms { font-size: 9.5px; color: #475569; line-height: 1.7; margin-bottom: 4px; }
    .terms strong { color: #1e293b; }
    .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
    .signature-table td { width: 50%; vertical-align: top; font-size: 9.5px; padding-top: 30px; border-top: 1px solid #cbd5e1; }
@endpush

@section('meta')
    <strong>Destinataire :</strong><br>
    <div class="recipient-block">
        @if($devis->prospect)
            {{ $devis->prospect->nom_contact ?? $devis->prospect->nom_entreprise }}<br>
            {{ $devis->prospect->nom_entreprise }}
        @elseif($devis->client)
            {{ $devis->client->nom_contact ?? $devis->client->nom }}<br>
            {{ $devis->client->nom }}
        @else
            Non spécifié
        @endif
    </div>
@endsection

@section('content')
    @if($devis->client)
        <p style="font-size:9.5px; color:#64748b; margin-top:-10px;">N° client : {{ $devis->client->code_client }}</p>
    @endif

    @if($devis->date_expiration)
        @php
            $joursRestants = now()->startOfDay()->diffInDays($devis->date_expiration->startOfDay(), false);
            $urgent = $joursRestants <= 7;
        @endphp
        <div class="expiration-note" style="background-color: {{ $urgent ? '#fee2e2' : '#fef3c7' }}; color: {{ $urgent ? '#991b1b' : '#92400e' }};">
            Devis valable jusqu'au {{ $devis->date_expiration->format('d/m/Y') }}
            @if($joursRestants >= 0)
                ({{ $joursRestants }} jour{{ $joursRestants > 1 ? 's' : '' }} restant{{ $joursRestants > 1 ? 's' : '' }})
            @else
                — délai expiré
            @endif
        </div>
    @endif

    <div class="section">
        <div class="section-header">Détail des prestations</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 28%;">Désignation</th>
                    <th>Description</th>
                    <th style="width: 10%; text-align:center;">Quantité</th>
                    <th style="width: 16%; text-align:right;">Prix U. HT</th>
                    <th style="width: 16%; text-align:right;">Total HT</th>
                    <th style="width: 8%; text-align:center;">TVA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devis->lignes as $ligne)
                    <tr>
                        <td><strong>{{ $ligne->designation }}</strong></td>
                        <td style="color:#64748b;">{{ $ligne->description ?? '-' }}</td>
                        <td style="text-align:center;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                        <td style="text-align:right;">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} {{ $currency }}</td>
                        <td style="text-align:right;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
                        <td style="text-align:center;">{{ $devis->taux_tva }} %</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <table class="totals-table">
        <tr>
            <td>Total HT</td>
            <td style="text-align:right;">{{ number_format($devis->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
        <tr>
            <td>TVA ({{ $devis->taux_tva }} %)</td>
            <td style="text-align:right;">{{ number_format($devis->montant_tva, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
        <tr class="grand-total">
            <td>Total TTC</td>
            <td style="text-align:right;">{{ number_format($devis->montant_ttc, 2, ',', ' ') }} {{ $currency }}</td>
        </tr>
    </table>

    <div class="clearfix"></div>

    @php
        $conditionsParts = [];
        if ($payment['deposit_percent'] > 0) {
            $decimales = $payment['deposit_percent'] == floor($payment['deposit_percent']) ? 0 : 2;
            $conditionsParts[] = number_format($payment['deposit_percent'], $decimales, ',', ' ') . ' % à la commande';
        }
        if ($payment['mode']) {
            $conditionsParts[] = 'solde par ' . $payment['mode'];
        }
        if ($payment['delay_days']) {
            $conditionsParts[] = $payment['delay_days'] . ' jours net';
        }
    @endphp
    <p class="terms">
        <strong>Durée de validité :</strong> {{ $devis->date_expiration ? 'jusqu\'au '.$devis->date_expiration->format('d/m/Y') : 'non spécifiée' }}<br>
        @if(!empty($conditionsParts))
            <strong>Conditions de règlement :</strong> {{ implode(', ', $conditionsParts) }}
        @endif
    </p>

    <p class="terms">Nous restons à votre disposition pour toute information complémentaire.</p>

    @if($devis->observations)
        <div class="section">
            <div class="section-header">Observations</div>
            <div class="info-box" style="font-size:9.5px; white-space:pre-wrap;">{{ $devis->observations }}</div>
        </div>
    @endif

    <p class="terms">Si ce devis vous convient, veuillez le retourner signé, daté et cacheté :</p>

    <table class="signature-table">
        <tr>
            <td>Pour {{ $company['name'] }} (cachet et signature)</td>
            <td>Pour le client<br>(précédée de la mention : « Lu et approuvé, bon pour accord »)</td>
        </tr>
    </table>

    @if($legalMentions)
        <p class="terms" style="margin-top:16px; color:#94a3b8;">{{ $legalMentions }}</p>
    @endif
@endsection
