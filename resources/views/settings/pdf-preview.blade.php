<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; margin: 30px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid {{ $company['color'] }}; padding-bottom: 15px; margin-bottom: 20px; }
        .logo { max-height: 60px; max-width: 180px; }
        .company-name { font-size: 18px; font-weight: bold; color: {{ $company['color'] }}; }
        .tagline { font-size: 10px; color: #64748b; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; background: {{ $company['color'] }}; color: #fff; font-size: 11px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f1f5f9; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .totals { margin-top: 15px; text-align: right; }
        .footer { margin-top: 40px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            @if($company['logo'])
                <img src="{{ storage_path('app/public/' . $company['logo']) }}" class="logo"><br>
            @endif
            <span class="company-name">{{ $company['name'] }}</span><br>
            <span class="tagline">{{ $company['tagline'] }}</span>
        </div>
        <div style="text-align:right;">
            <span class="badge">APERÇU — DEVIS D'EXEMPLE</span>
            <p style="margin-top:8px;">{{ $sample->numero }}<br>{{ $sample->date_emission->format('d/m/Y') }}</p>
        </div>
    </div>

    <p><strong>Client :</strong> {{ $sample->client_nom }}<br>{{ $sample->client_adresse }}</p>

    <table>
        <thead><tr><th>Désignation</th><th>Qté</th><th>Prix Unit.</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($sample->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->designation }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire, 2) }}</td>
                    <td>{{ number_format($ligne->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total HT : {{ number_format($sample->montant_ht, 2) }}</p>
        <p>TVA : {{ number_format($sample->montant_tva, 2) }}</p>
        <p style="font-weight:bold; color: {{ $company['color'] }};">Total TTC : {{ number_format($sample->montant_ttc, 2) }}</p>
    </div>

    <div class="footer">
        {{ $company['name'] }} — {{ $company['address'] }}<br>
        Tél: {{ $company['phone'] }} @if($company['fax']) · Fax: {{ $company['fax'] }} @endif · {{ $company['email'] }} @if($company['website']) · {{ $company['website'] }} @endif<br>
        @if($company['ice']) ICE: {{ $company['ice'] }} @endif @if($company['rc']) · {{ $company['rc'] }} @endif
    </div>
</body>
</html>
