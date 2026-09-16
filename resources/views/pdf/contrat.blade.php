<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Contrat' }} — {{ $documentReference ?? '' }}</title>
    <style>
        @page { margin: 60px 55px 60px 55px; }
        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Times, serif;
            color: #111111;
            font-size: 11.5px;
            line-height: 1.55;
        }

        .page-tag {
            position: fixed;
            top: -35px;
            right: 0;
            font-size: 9px;
            color: #333333;
        }
        .page-tag:after { content: "Page " counter(page) " sur " counter(pages); }

        .titre-bloc { text-align: center; margin-bottom: 26px; }
        .titre-bloc .t1 { font-size: 13px; font-weight: bold; text-transform: uppercase; }
        .titre-bloc .t2 { font-size: 13px; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
        .titre-bloc .ref { font-size: 12px; font-weight: bold; margin-top: 10px; letter-spacing: 0.5px; }

        p { margin: 0 0 10px 0; text-align: justify; }

        .article-titre {
            font-weight: bold;
            text-decoration: underline;
            margin: 18px 0 8px 0;
        }

        .sla-table { width: 100%; border-collapse: collapse; margin: 6px 0 14px 0; font-size: 10.5px; }
        .sla-table th, .sla-table td { border: 1px solid #000000; padding: 5px 8px; text-align: left; }
        .sla-table th { font-weight: bold; }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 45px; }
        .signature-table td { width: 50%; vertical-align: top; font-size: 10.5px; padding-top: 45px; }
        .signature-table .titre { font-weight: bold; text-decoration: underline; }

        .pdf-footer {
            position: fixed;
            bottom: -45px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #222222;
            border-top: 1px solid #999999;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <div class="page-tag"></div>

    <div class="titre-bloc">
        <div class="t1">Contrat d'Assistance et de Maintenance Annuel par Intervention</div>
        <div class="t2">pour Réseaux Informatiques, Téléphoniques et Systèmes de Sécurité</div>
        <div class="ref">{{ strtoupper($documentTitle ?? 'CONTRAT') }} N° {{ $documentReference ?? '……………………………' }}</div>
    </div>

    <p>Il a été arrêté et convenu entre les soussignés :</p>
    <p>
        La société <strong>{{ $company['name'] ?? 'TechniTrack' }}</strong>, légalement représentée par son représentant légal,
        ci-après désignée « le <strong>Prestataire</strong> ».
    </p>
    <p>Et</p>
    <p>
        La société <strong>{{ $client->nom ?? '……………………………' }}</strong>,
        légalement représentée par {{ $client->nom_contact ?? 'son représentant' }},
        ci-après désignée « le <strong>Client</strong> ».
    </p>
    <p>
        Un {{ strtolower($contrat['type'] ?? 'contrat de maintenance') }} du réseau informatique, téléphonique et des systèmes
        de sécurité de la société {{ $client->nom ?? '' }}, conclu avec la société {{ $company['name'] ?? 'TechniTrack' }}.
    </p>

    @php
        $chiffresRomains = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
        $numero = 1;
    @endphp
    @foreach($contrat['clauses'] as $clause)
        <div class="article-titre">ARTICLE {{ $chiffresRomains[$numero - 1] ?? $numero }} : {{ strtoupper($clause['titre']) }}.</div>
        <p>{{ $clause['texte'] }}</p>
        @php $numero++; @endphp
    @endforeach

    @if(!empty($contrat['sla']))
        <div class="article-titre">ARTICLE {{ $chiffresRomains[$numero - 1] ?? $numero }} : NIVEAUX DE SERVICE ET DÉLAIS D'INTERVENTION.</div>
        <p>
            En cas de panne ou de demande d'intervention, le Client soumettra sa demande via le portail Client TechniTrack
            ou le module Support. Le Prestataire s'engage à y donner suite selon le délai garanti ci-dessous, déterminé
            par le degré de priorité de la demande :
        </p>
        <table class="sla-table">
            <thead>
                <tr>
                    <th style="width:22%;">Priorité</th>
                    <th style="width:28%;">Délai d'intervention garanti</th>
                    <th style="width:50%;">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contrat['sla'] as $niveau)
                    <tr>
                        <td>{{ $niveau['priorite'] }}</td>
                        <td>{{ $niveau['delai'] }}</td>
                        <td>{{ $niveau['description'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @php $numero++; @endphp
    @endif

    <div class="article-titre">ARTICLE {{ $chiffresRomains[$numero - 1] ?? $numero }} : DURÉE DU CONTRAT.</div>
    <p>
        Il est conclu du {{ $contrat['date_debut']->format('d/m/Y') }} au {{ $contrat['date_fin']->format('d/m/Y') }},
        {{ $contrat['reconduction'] ?? 'renouvelable par tacite reconduction, à moins de dénonciation par l\'une des parties' }}.
        La dénonciation devrait être faite par l'une ou l'autre des parties au moins 30 jours avant la date d'expiration
        de la période contractuelle en cours.
    </p>

    <table class="signature-table">
        <tr>
            <td>
                <div class="titre">Pour le Prestataire</div>
                {{ $company['name'] ?? 'TechniTrack' }}<br>Cachet et signature
            </td>
            <td>
                <div class="titre">Pour le Client</div>
                {{ $client->nom ?? '' }}<br>Précédée de la mention « Lu et approuvé »
            </td>
        </tr>
    </table>

    <div class="pdf-footer">
        {{ $company['name'] ?? 'TechniTrack' }}
        @if(!empty($company['address'])) : {{ $company['address'] }}@endif
        @if(!empty($company['phone'])) — TÉL. : {{ $company['phone'] }}@endif
        @if(!empty($company['email'])) — EMAIL : {{ $company['email'] }}@endif
    </div>
</body>
</html>
