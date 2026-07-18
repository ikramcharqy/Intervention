<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Intervention - {{ $rapport->intervention->code_intervention ?? 'N/A' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .header .title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .header .meta {
            text-align: right;
            color: #666;
        }
        .section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #4f46e5;
            background-color: #f3f4f6;
            padding: 5px 8px;
            margin-bottom: 8px;
            border-left: 3px solid #4f46e5;
        }
        .grid {
            width: 100%;
            margin-bottom: 10px;
        }
        .grid td {
            padding: 4px 0;
            vertical-align: top;
        }
        .grid td.label {
            font-weight: bold;
            color: #4b5563;
            width: 25%;
        }
        .grid td.valeur {
            width: 25%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .table th, .table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: bold;
        }
        .photo-grid {
            margin-top: 10px;
        }
        .photo-item {
            display: inline-block;
            width: 45%;
            margin-right: 4%;
            margin-bottom: 10px;
            vertical-align: top;
        }
        .photo-item img {
            width: 100%;
            max-height: 180px;
            border: 1px solid #e5e7eb;
        }
        .photo-item .caption {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
            text-align: center;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 48%;
            border: 1px dashed #d1d5db;
            padding: 10px;
            height: 120px;
            vertical-align: top;
        }
        .signature-title {
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .signature-img {
            max-width: 100%;
            max-height: 80px;
        }
        .signature-text {
            font-family: monospace;
            color: #4b5563;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <span class="title">RAPPORT D'INTERVENTION</span><br>
                    <span style="font-size: 13px; color: #4b5563;">Code : {{ $rapport->intervention->code_intervention ?? 'N/A' }}</span>
                </td>
                <td class="meta">
                    <strong>Statut :</strong> {{ $rapport->intervention->statut ?? 'N/A' }}<br>
                    <strong>Date d'édition :</strong> {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- 1. Informations Générales --}}
    <div class="section">
        <div class="section-title">Informations Générales</div>
        <table class="grid">
            <tr>
                <td class="label">Client :</td>
                <td class="valeur">{{ $rapport->intervention->chantier->client->nom ?? '-' }}</td>
                <td class="label">Technicien :</td>
                <td class="valeur">{{ $rapport->intervention->technicien->prenom ?? '' }} {{ $rapport->intervention->technicien->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Chantier :</td>
                <td class="valeur">{{ $rapport->intervention->chantier->nom ?? '-' }}</td>
                <td class="label">Type d'intervention :</td>
                <td class="valeur">{{ $rapport->intervention->typeIntervention->nom ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Emplacement :</td>
                <td class="valeur">{{ $rapport->intervention->emplacement->nom ?? '-' }}</td>
                <td class="label">Priorité :</td>
                <td class="valeur">{{ $rapport->intervention->priorite ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- 2. Dates et Suivi --}}
    <div class="section">
        <div class="section-title">Dates et Suivi de Présence</div>
        <table class="grid">
            <tr>
                <td class="label">Début prévu :</td>
                <td class="valeur">{{ $rapport->intervention->date_prevue_debut ? $rapport->intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</td>
                <td class="label">Début réel :</td>
                <td class="valeur">{{ $rapport->intervention->date_reelle_debut ? $rapport->intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Fin prévue :</td>
                <td class="valeur">{{ $rapport->intervention->date_prevue_fin ? $rapport->intervention->date_prevue_fin->format('d/m/Y H:i') : '-' }}</td>
                <td class="label">Fin réelle :</td>
                <td class="valeur">{{ $rapport->intervention->date_reelle_fin ? $rapport->intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Mode de suivi :</td>
                <td class="valeur">{{ $rapport->intervention->mode_suivi ?? 'Manuel' }}</td>
                <td class="label">Durée réelle :</td>
                <td class="valeur">{{ $rapport->intervention->duree_reelle ? $rapport->intervention->duree_reelle . ' min' : '-' }}</td>
            </tr>
            @php
                $tracking = $rapport->intervention->trackingSessions->first();
            @endphp
            @if($tracking)
                <tr>
                    <td class="label">Coordonnées GPS initiales :</td>
                    <td class="valeur" colspan="3">
                        @if($tracking->latitude && $tracking->longitude)
                            Lat: {{ $tracking->latitude }}, Lon: {{ $tracking->longitude }}
                        @else
                            Non capturées
                        @endif
                    </td>
                </tr>
                @if($tracking->qr_code_scan)
                    <tr>
                        <td class="label">Scan QR Code :</td>
                        <td class="valeur" colspan="3">{{ $tracking->qr_code_scan }} (Validé)</td>
                    </tr>
                @endif
            @elseif($rapport->intervention->emplacement)
                <tr>
                    <td class="label">Coordonnées Emplacement :</td>
                    <td class="valeur" colspan="3">
                        @if($rapport->intervention->emplacement->latitude && $rapport->intervention->emplacement->longitude)
                            Lat: {{ $rapport->intervention->emplacement->latitude }}, Lon: {{ $rapport->intervention->emplacement->longitude }}
                        @else
                            Non configurées
                        @endif
                    </td>
                </tr>
                @if($rapport->intervention->emplacement->qr_code)
                    <tr>
                        <td class="label">QR Code Emplacement :</td>
                        <td class="valeur" colspan="3">{{ $rapport->intervention->emplacement->qr_code }}</td>
                    </tr>
                @endif
            @endif
        </table>
    </div>

    {{-- 3. Réponses au Formulaire Dynamique --}}
    @if($rapport->reponses->isNotEmpty())
        <div class="section">
            <div class="section-title">Formulaire d'Intervention</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Question</th>
                        <th style="width: 60%;">Réponse</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport->reponses as $reponse)
                        @php
                            $question = $reponse->question;
                            if (!$question) continue;
                        @endphp
                        <tr>
                            <td><strong>{{ $question->question }}</strong></td>
                            <td>
                                @if(in_array($question->type_reponse, ['Photo', 'Signature', 'Document']))
                                    @if($reponse->reponse_fichier)
                                        @php
                                            $filePath = storage_path('app/public/' . $reponse->reponse_fichier);
                                            $exists = file_exists($filePath);
                                        @endphp
                                        @if($exists && in_array(pathinfo($filePath, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="data:image/{{ pathinfo($filePath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($filePath)) }}" style="max-width: 250px; max-height: 150px; display: block; margin-top: 5px;">
                                        @else
                                            <span style="color: #666;">Fichier joint : {{ basename($reponse->reponse_fichier) }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                @elseif($question->type_reponse === 'OuiNon')
                                    {{ $reponse->reponse_texte === '1' ? 'Oui' : 'Non' }}
                                @elseif($question->type_reponse === 'Checkbox')
                                    @php
                                        $choices = \App\Models\ChoixQuestion::whereIn('id', is_array($reponse->reponse_texte) ? $reponse->reponse_texte : explode(',', $reponse->reponse_texte))->pluck('valeur')->toArray();
                                    @endphp
                                    {{ implode(', ', $choices) }}
                                @elseif($question->type_reponse === 'Radio' || $question->type_reponse === 'Liste')
                                    {{ $reponse->choixQuestion->valeur ?? $reponse->reponse_texte }}
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

    {{-- 4. Matériaux utilisés --}}
    @if($rapport->intervention->materiaux->isNotEmpty())
        @php
            $coutTotal = $rapport->intervention->materiaux->sum(function($m) {
                return $m->quantite * ($m->materiau->prix_unitaire ?? 0);
            });
        @endphp
        <div class="section">
            <div class="section-title">Matériaux utilisés</div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10%;">Réf.</th>
                        <th style="width: 30%;">Matériau</th>
                        <th style="width: 15%;">Quantité</th>
                        <th style="width: 15%;">Prix unit. HT</th>
                        <th style="width: 15%;">Coût total HT</th>
                        <th style="width: 15%;">Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport->intervention->materiaux as $item)
                        <tr>
                            <td style="font-family: monospace; font-size: 9px;">{{ $item->materiau->reference ?? '-' }}</td>
                            <td><strong>{{ $item->materiau->nom ?? 'Inconnu' }}</strong></td>
                            <td>{{ $item->quantite }} {{ $item->unite ?? ($item->materiau->unite ?? '') }}</td>
                            <td>
                                @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                    {{ number_format($item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                    {{ number_format($item->quantite * $item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->commentaire ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @if($coutTotal > 0)
                    <tfoot>
                        <tr style="background-color: #f3f4f6;">
                            <td colspan="4" style="text-align: right; font-weight: bold; padding: 6px 8px;">Total HT :</td>
                            <td style="font-weight: bold; padding: 6px 8px;">{{ number_format($coutTotal, 2, ',', ' ') }} €</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @endif

    {{-- 5. Observations --}}
    @if($rapport->commentaire || $rapport->intervention->description || $rapport->intervention->observations)
        <div class="section">
            <div class="section-title">Observations / Consignes</div>
            @if($rapport->intervention->description)
                <div style="margin-bottom: 10px;">
                    <strong>Description initiale :</strong><br>
                    <span style="color: #4b5563;">{{ $rapport->intervention->description }}</span>
                </div>
            @endif
            @if($rapport->intervention->observations)
                <div style="margin-bottom: 10px;">
                    <strong>Consignes de planification :</strong><br>
                    <span style="color: #4b5563;">{{ $rapport->intervention->observations }}</span>
                </div>
            @endif
            @if($rapport->commentaire)
                <div>
                    <strong>Commentaires du rapport :</strong><br>
                    <span style="color: #4b5563;">{{ $rapport->commentaire }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- 5. Signatures --}}
    <table class="signatures">
        <tr>
            <td class="signature-box" style="margin-right: 4%;">
                <div class="signature-title">Signature du Technicien</div>
                @if($rapport->signature_technicien)
                    @php
                        $techSigPath = storage_path('app/public/' . $rapport->signature_technicien);
                        $techSigExists = file_exists($techSigPath);
                    @endphp
                    @if($techSigExists && in_array(pathinfo($techSigPath, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="data:image/{{ pathinfo($techSigPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($techSigPath)) }}" class="signature-img">
                    @else
                        <div class="signature-text">{{ $rapport->signature_technicien }}</div>
                    @endif
                @else
                    <span style="color: #9ca3af;">Non signée</span>
                @endif
            </td>
            <td class="signature-box">
                <div class="signature-title">Signature du Client</div>
                @if($rapport->signature_client)
                    @php
                        $clientSigPath = storage_path('app/public/' . $rapport->signature_client);
                        $clientSigExists = file_exists($clientSigPath);
                    @endphp
                    @if($clientSigExists && in_array(pathinfo($clientSigPath, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="data:image/{{ pathinfo($clientSigPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($clientSigPath)) }}" class="signature-img">
                    @else
                        <div class="signature-text">{{ $rapport->signature_client }}</div>
                    @endif
                @else
                    <span style="color: #9ca3af;">Non signée</span>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
