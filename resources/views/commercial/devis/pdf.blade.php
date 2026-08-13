<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis {{ $devis->reference ?? $devis->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        .header {
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .devis-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e3a8a;
            text-align: right;
            margin-top: -30px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .info-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 4px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #1e3a8a;
            color: white;
            padding: 10px;
            font-size: 13px;
            text-align: left;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals-table {
            width: 300px;
            float: right;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 10px;
            font-size: 13px;
        }
        .totals-table tr.grand-total {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
        }
        .observations {
            margin-top: 50px;
            clear: both;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">CRM INTERVENTION</div>
        <div class="devis-title">DEVIS</div>
    </div>

    <table class="details-table">
        <tr>
            <td>
                <div class="section-title">Émetteur</div>
                <strong>CRM Intervention</strong><br>
                Commercial : {{ $devis->commercial ? $devis->commercial->name : 'N/A' }}<br>
                Email : {{ $devis->commercial ? $devis->commercial->email : 'N/A' }}<br>
            </td>
            <td>
                <div class="section-title">Destinataire</div>
                @if($devis->prospect)
                    <strong>{{ $devis->prospect->nom_entreprise }}</strong><br>
                    Contact : {{ $devis->prospect->nom_contact }}<br>
                    Email : {{ $devis->prospect->email }}<br>
                    Téléphone : {{ $devis->prospect->telephone }}<br>
                    Adresse : {{ $devis->prospect->adresse }}<br>
                @elseif($devis->client)
                    <strong>{{ $devis->client->nom }}</strong><br>
                    Contact : {{ $devis->client->nom_contact }}<br>
                    Email : {{ $devis->client->email }}<br>
                    Téléphone : {{ $devis->client->telephone }}<br>
                    Adresse : {{ $devis->client->adresse_facturation }}<br>
                @else
                    <strong>Non spécifié</strong>
                @endif
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table>
            <tr>
                <td><strong>Référence :</strong> {{ $devis->reference ?? 'DEVIS-'.$devis->id }}</td>
                <td><strong>Date d'émission :</strong> {{ $devis->date_emission ? $devis->date_emission->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Statut :</strong> {{ $devis->statut }}</td>
                <td><strong>Date d'expiration :</strong> {{ $devis->date_expiration ? $devis->date_expiration->format('d/m/Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30%;">Désignation</th>
                <th>Description</th>
                <th class="text-center" style="width: 12%;">Quantité</th>
                <th class="text-right" style="width: 18%;">Prix U. HT</th>
                <th class="text-right" style="width: 18%;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($devis->lignes as $ligne)
                <tr>
                    <td><strong>{{ $ligne->designation }}</strong></td>
                    <td style="color: #666;">{{ $ligne->description ?? '-' }}</td>
                    <td class="text-center">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
                    <td class="text-right">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Total HT :</td>
            <td class="text-right">{{ number_format($devis->montant_ht, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td>TVA ({{ $devis->taux_tva }}%) :</td>
            <td class="text-right">{{ number_format($devis->montant_tva, 2, ',', ' ') }} €</td>
        </tr>
        <tr class="grand-total">
            <td>Total TTC :</td>
            <td class="text-right">{{ number_format($devis->montant_ttc, 2, ',', ' ') }} €</td>
        </tr>
    </table>

    @if($devis->observations)
        <div class="observations">
            <div class="section-title">Observations / Conditions</div>
            <p style="white-space: pre-wrap; font-size: 12px; color: #555;">{{ $devis->observations }}</p>
        </div>
    @endif

</body>
</html>
