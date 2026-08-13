<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Intervention - {{ $rapport->intervention->code_intervention ?? 'N/A' }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.5;
            background-color: #ffffff;
        }
        
        /* Header Corporate */
        .brand-header {
            width: 100%;
            border-bottom: 3px solid #4338ca;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand-header table {
            width: 100%;
        }
        .brand-logo {
            font-size: 22px;
            font-weight: 900;
            color: #1e1b4b;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-logo span {
            color: #4f46e5;
        }
        .doc-title {
            font-size: 16px;
            font-weight: 800;
            color: #4338ca;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #e0e7ff;
            color: #3730a3;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 4px;
        }
        .meta-right {
            text-align: right;
            font-size: 9.5px;
            color: #64748b;
        }
        .meta-right strong {
            color: #0f172a;
        }

        /* Sections */
        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .section-header {
            font-size: 11px;
            font-weight: 800;
            color: #1e1b4b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #f1f5f9;
            padding: 6px 10px;
            border-left: 4px solid #4338ca;
            margin-bottom: 10px;
            border-radius: 0 4px 4px 0;
        }

        /* Metric Cards Grid */
        .grid-cards {
            width: 100%;
            margin-bottom: 10px;
        }
        .grid-cards td {
            width: 25%;
            padding: 4px;
            vertical-align: top;
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px;
        }
        .card-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .card-value {
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 7px 8px;
            vertical-align: top;
            font-size: 9.5px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Photo Grid */
        .photo-grid {
            width: 100%;
            margin-top: 8px;
        }
        .photo-card {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px;
            box-sizing: border-box;
        }
        .photo-card img {
            width: 100%;
            max-height: 160px;
            border-radius: 4px;
            object-fit: cover;
        }

        /* Signatures */
        .signatures-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-cell {
            width: 48%;
            border: 1.5px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            vertical-align: top;
            height: 110px;
        }
        .sig-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .sig-img {
            max-width: 100%;
            max-height: 70px;
        }

        /* Footer */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header corporate -->
    <div class="brand-header">
        <table>
            <tr>
                <td>
                    <div class="brand-logo">INTERVENTION<span>PRO</span></div>
                    <div class="doc-title">Rapport d'Intervention Technique</div>
                    <div class="doc-badge">Réf: {{ $rapport->intervention->code_intervention ?? 'INT-0000' }}</div>
                </td>
                <td class="meta-right">
                    <strong>Statut Officiel :</strong> <span style="color:#059669; font-weight:bold;">{{ strtoupper($rapport->intervention->statut ?? 'TERMINÉ') }}</span><br>
                    <strong>Date d'Édition :</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>Client :</strong> {{ $rapport->intervention->chantier->client->nom ?? 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Synthèse de l'Intervention -->
    <div class="section">
        <div class="section-header">1. Informations Générales & Intervention</div>
        <table class="grid-cards">
            <tr>
                <td>
                    <div class="card-box">
                        <div class="card-label">Client</div>
                        <div class="card-value">{{ $rapport->intervention->chantier->client->nom ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Chantier</div>
                        <div class="card-value">{{ $rapport->intervention->chantier->nom ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Technicien Référent</div>
                        <div class="card-value">{{ $rapport->intervention->technicien->name ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Type d'Intervention</div>
                        <div class="card-value">{{ $rapport->intervention->typeIntervention->nom ?? '-' }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card-box">
                        <div class="card-label">Début Réel</div>
                        <div class="card-value">{{ $rapport->intervention->date_reelle_debut ? $rapport->intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Fin Réelle</div>
                        <div class="card-value">{{ $rapport->intervention->date_reelle_fin ? $rapport->intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Durée Réelle</div>
                        <div class="card-value">{{ $rapport->intervention->duree_reelle ? $rapport->intervention->duree_reelle . ' Min' : '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Emplacement</div>
                        <div class="card-value">{{ $rapport->intervention->emplacement->nom ?? 'Standard' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. Formulaire Technique & Constats -->
    @if($rapport->reponses && $rapport->reponses->isNotEmpty())
        <div class="section">
            <div class="section-header">2. Questions & Formulaire de Validation Terrain</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Point de Contrôle / Question</th>
                        <th style="width: 55%;">Constat / Réponse Technicien</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport->reponses as $reponse)
                        @php $q = $reponse->question; if(!$q) continue; @endphp
                        <tr>
                            <td><strong>{{ $q->question }}</strong></td>
                            <td>
                                @if(in_array($q->type_reponse, ['Photo', 'Signature']))
                                    @if($reponse->reponse_fichier)
                                        @php
                                            $filePath = storage_path('app/public/' . $reponse->reponse_fichier);
                                            $exists = file_exists($filePath);
                                        @endphp
                                        @if($exists)
                                            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($filePath)) }}" style="max-height: 120px; border-radius: 4px; border: 1px solid #cbd5e1;">
                                        @else
                                            [Image enregistrée : {{ basename($reponse->reponse_fichier) }}]
                                        @endif
                                    @else
                                        <span style="color:#94a3b8;">Non fournie</span>
                                    @endif
                                @elseif($q->type_reponse === 'OuiNon')
                                    <span style="font-weight: bold; color: {{ $reponse->reponse_texte === '1' ? '#059669' : '#dc2626' }};">
                                        {{ $reponse->reponse_texte === '1' ? '✔ OUI / VALIDÉ' : '✖ NON / CONFORME PAS' }}
                                    </span>
                                @else
                                    {{ $reponse->reponse_texte ?? $reponse->reponse_nombre ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- 3. Matériaux & Fournitures -->
    @if($rapport->intervention->materiaux && $rapport->intervention->materiaux->isNotEmpty())
        <div class="section">
            <div class="section-header">3. Matériaux & Pièces Consommées</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation du Matériau</th>
                        <th style="text-align: center;">Quantité</th>
                        <th style="text-align: right;">Prix Unitaire HT</th>
                        <th style="text-align: right;">Montant Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalGeneral = 0; @endphp
                    @foreach($rapport->intervention->materiaux as $item)
                        @php 
                            $pu = $item->materiau->prix_unitaire ?? 0;
                            $st = $item->quantite * $pu;
                            $totalGeneral += $st;
                        @endphp
                        <tr>
                            <td style="font-family: monospace;">{{ $item->materiau->reference ?? 'REF-STD' }}</td>
                            <td><strong>{{ $item->materiau->nom ?? 'Matériau' }}</strong></td>
                            <td style="text-align: center; font-weight: bold;">{{ $item->quantite }}</td>
                            <td style="text-align: right;">{{ number_format($pu, 2, ',', ' ') }} MAD</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($st, 2, ',', ' ') }} MAD</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="4" style="text-align: right; padding: 8px;">TOTAL MATÉRIAUX HT :</td>
                        <td style="text-align: right; color: #4338ca; font-size: 11px; padding: 8px;">{{ number_format($totalGeneral, 2, ',', ' ') }} MAD</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- 4. Commentaires & Remarques -->
    @if($rapport->commentaire)
        <div class="section">
            <div class="section-header">4. Conclusions & Observations du Technicien</div>
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 6px; font-size: 9.5px; color: #334155;">
                {!! nl2br(e($rapport->commentaire)) !!}
            </div>
        </div>
    @endif

    <!-- 5. Valider & Signatures -->
    <table class="signatures-table">
        <tr>
            <td class="sig-cell" style="margin-right: 4%;">
                <div class="sig-title">Visa / Signature du Technicien</div>
                @if($rapport->signature_technicien)
                    @php
                        $techPath = storage_path('app/public/' . $rapport->signature_technicien);
                    @endphp
                    @if(file_exists($techPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($techPath)) }}" class="sig-img">
                    @else
                        <div style="font-family: monospace; color: #4338ca; font-weight: bold;">{{ $rapport->signature_technicien }}</div>
                    @endif
                @else
                    <div style="color: #94a3b8; font-style: italic; margin-top: 20px;">Signature numérique validée à la clôture</div>
                @endif
            </td>
            <td class="sig-cell">
                <div class="sig-title">Visa / Signature du Client</div>
                @if($rapport->signature_client)
                    @php
                        $clientPath = storage_path('app/public/' . $rapport->signature_client);
                    @endphp
                    @if(file_exists($clientPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($clientPath)) }}" class="sig-img">
                    @else
                        <div style="font-family: monospace; color: #4338ca; font-weight: bold;">{{ $rapport->signature_client }}</div>
                    @endif
                @else
                    <div style="color: #94a3b8; font-style: italic; margin-top: 20px;">Accusé de réception client enregistré</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="pdf-footer">
        Document généré automatiquement par la plateforme d'Intervention Technique • Tous droits réservés
    </div>

</body>
</html>
