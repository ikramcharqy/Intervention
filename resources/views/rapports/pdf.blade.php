<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Intervention - {{ $rapport->intervention->code_intervention ?? 'N/A' }}</title>
    @php
        $cName    = \App\Models\Setting::get('company_name', 'TechInterv Solutions');
        $cTagline = \App\Models\Setting::get('company_tagline', 'Excellence en Maintenance & Interventions Techniques');
        $cAddress = \App\Models\Setting::get('company_address', '12, Boulevard Hassan II – Casablanca, Maroc 20250');
        $cPhone   = \App\Models\Setting::get('company_phone', '+212 5 22 45 88 99');
        $cFax     = \App\Models\Setting::get('company_fax', '+212 5 22 45 88 00');
        $cEmail   = \App\Models\Setting::get('company_email', 'contact@techinterv.ma');
        $cWebsite = \App\Models\Setting::get('company_website', 'www.techinterv.ma');
        $cIce     = \App\Models\Setting::get('company_ice', '002847593000088');
        $cRc      = \App\Models\Setting::get('company_rc', 'RC 485920 – Casablanca');
        $cLogo    = \App\Models\Setting::get('company_logo', '');
        $cColor   = \App\Models\Setting::get('company_color', '#4338CA');

        $logoBase64 = null;
        if ($cLogo) {
            $logoPath = storage_path('app/public/' . $cLogo);
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
    @endphp
    <style>
        @page {
            margin: 25px 30px 45px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.45;
            background-color: #ffffff;
        }

        /* Header Corporate */
        .brand-header {
            width: 100%;
            border-bottom: 3px solid {{ $cColor }};
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .brand-header table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-logo-text {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-tagline {
            font-size: 8.5px;
            color: #64748b;
            font-weight: 600;
            margin-top: 1px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: 800;
            color: {{ $cColor }};
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .doc-badge {
            display: inline-block;
            padding: 2px 7px;
            background-color: #e0e7ff;
            color: #3730a3;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 3px;
        }
        .meta-right {
            text-align: right;
            font-size: 9px;
            color: #64748b;
            vertical-align: top;
        }
        .meta-right strong {
            color: #0f172a;
        }

        /* Sections */
        .section {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .section-header {
            font-size: 10.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            background-color: #f1f5f9;
            padding: 5px 9px;
            border-left: 4px solid {{ $cColor }};
            margin-bottom: 8px;
            border-radius: 0 4px 4px 0;
        }

        /* Metric Cards Grid */
        .grid-cards {
            width: 100%;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .grid-cards td {
            width: 25%;
            padding: 3px;
            vertical-align: top;
        }
        .card-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 6px;
        }
        .card-label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .card-value {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 7px;
            text-align: left;
        }
        .data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 7px;
            vertical-align: top;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Photo Grid Categorized */
        .photo-category-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            margin-top: 8px;
            margin-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }
        .photo-card {
            width: 31%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
            margin-bottom: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 3px;
            box-sizing: border-box;
            background-color: #f8fafc;
        }
        .photo-card img {
            width: 100%;
            height: 110px;
            border-radius: 3px;
            object-fit: cover;
        }
        .photo-caption {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
            text-align: center;
            font-weight: 600;
        }

        /* Signatures */
        .signatures-table {
            width: 100%;
            margin-top: 15px;
            page-break-inside: avoid;
            border-collapse: collapse;
        }
        .sig-cell {
            width: 48%;
            border: 1.5px dashed #cbd5e1;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 10px;
            vertical-align: top;
            height: 100px;
        }
        .sig-title {
            font-size: 9px;
            font-weight: 800;
            color: #334155;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        .sig-img {
            max-width: 100%;
            max-height: 65px;
        }

        /* Footer Fixed */
        .pdf-footer {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            height: 35px;
            font-size: 7.5px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            text-align: center;
            line-height: 1.3;
        }
        .pdf-footer strong {
            color: #1e293b;
        }
    </style>
</head>
<body>

    <!-- Header Corporate -->
    <div class="brand-header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-height: 45px; max-width: 180px; margin-bottom: 3px;">
                    @else
                        <div class="brand-logo-text">{{ $cName }}</div>
                    @endif
                    <div class="brand-tagline">{{ $cTagline }}</div>
                    <div class="doc-title">Rapport Officiel d'Intervention Technique</div>
                    <div class="doc-badge">Réf: {{ $rapport->intervention->code_intervention ?? 'INT-0000' }}</div>
                </td>
                <td class="meta-right">
                    <strong>Statut Clôture :</strong>
                    <span style="color:#059669; font-weight:bold; font-size:10px;">
                        {{ strtoupper($rapport->intervention->statut ?? 'TERMINÉ') }}
                    </span><br>
                    <strong>Édité le :</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    <strong>Client :</strong> {{ $rapport->intervention->chantier->client->nom ?? 'N/A' }}<br>
                    <strong>Technicien :</strong> {{ $rapport->intervention->technicien->name ?? 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Synthèse Générale & Intervenants -->
    <div class="section">
        <div class="section-header">1. Informations Générales & Intervenants</div>
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
                        <div class="card-label">Chantier / Site</div>
                        <div class="card-value">{{ $rapport->intervention->chantier->nom ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="card-box">
                        <div class="card-label">Emplacement</div>
                        <div class="card-value">{{ $rapport->intervention->emplacement->nom ?? 'Standard' }}</div>
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
                        <div class="card-label">Priorité & Mode</div>
                        <div class="card-value">{{ $rapport->intervention->priorite ?? 'Normale' }} ({{ $rapport->intervention->mode_suivi ?? 'GPS' }})</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 2. Travaux Réalisés, Observations & Recommandations -->
    <div class="section">
        <div class="section-header">2. Compte-Rendu Technique & Constats</div>
        @if($rapport->travaux_effectues)
            <div style="margin-bottom: 6px;">
                <strong style="color: #334155;">Travaux Réalisés :</strong>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; border-radius: 4px; font-size: 9px; margin-top: 2px;">
                    {!! nl2br(e($rapport->travaux_effectues)) !!}
                </div>
            </div>
        @endif

        @if($rapport->observations)
            <div style="margin-bottom: 6px;">
                <strong style="color: #334155;">Observations :</strong>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; border-radius: 4px; font-size: 9px; margin-top: 2px;">
                    {!! nl2br(e($rapport->observations)) !!}
                </div>
            </div>
        @endif

        @if($rapport->recommandations)
            <div style="margin-bottom: 6px;">
                <strong style="color: #334155;">Recommandations du Technicien :</strong>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; border-radius: 4px; font-size: 9px; margin-top: 2px;">
                    {!! nl2br(e($rapport->recommandations)) !!}
                </div>
            </div>
        @endif

        @if($rapport->commentaire && !$rapport->travaux_effectues)
            <div style="margin-bottom: 6px;">
                <strong style="color: #334155;">Commentaires Globaux :</strong>
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 6px; border-radius: 4px; font-size: 9px; margin-top: 2px;">
                    {!! nl2br(e($rapport->commentaire)) !!}
                </div>
            </div>
        @endif
    </div>

    <!-- 3. Formulaire Technique Terrain -->
    @if($rapport->reponses && $rapport->reponses->isNotEmpty())
        <div class="section">
            <div class="section-header">3. Réponses au Formulaire Dynamique Terrain</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Point de Contrôle / Question</th>
                        <th style="width: 55%;">Constat / Réponse Technicien</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rapport->reponses as $reponse)
                        @php $q = $reponse->question; if(!$q || $q->type_reponse === 'Materiaux') continue; @endphp
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
                                            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($filePath)) }}" style="max-height: 90px; border-radius: 3px; border: 1px solid #cbd5e1;">
                                        @else
                                            [Fichier enregistré : {{ basename($reponse->reponse_fichier) }}]
                                        @endif
                                    @else
                                        <span style="color:#94a3b8;">Non fournie</span>
                                    @endif
                                @elseif(in_array($q->type_reponse, ['OuiNon', 'Oui_Non']))
                                    <span style="font-weight: bold; color: {{ in_array(strtolower($reponse->reponse_texte), ['1','oui','true']) ? '#059669' : '#dc2626' }};">
                                        {{ in_array(strtolower($reponse->reponse_texte), ['1','oui','true']) ? '✔ OUI / CONFORME' : '✖ NON / ANOMALIE' }}
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

    <!-- 4. Photos Terrain (Avant / Après / Problèmes Détectés) -->
    @if($rapport->photos && $rapport->photos->isNotEmpty())
        <div class="section">
            <div class="section-header">4. Galerie Photos Terrain Labellisées</div>

            @php
                $photosAvant = $rapport->photos->filter(fn($p) => $p->type_photo === 'avant');
                $photosApres = $rapport->photos->filter(fn($p) => $p->type_photo === 'apres');
                $photosProbleme = $rapport->photos->filter(fn($p) => $p->type_photo === 'probleme' || (empty($p->type_photo) && !in_array($p->type_photo, ['avant','apres'])));
            @endphp

            @if($photosAvant->isNotEmpty())
                <div class="photo-category-title">📷 Photos AVANT Intervention</div>
                <div>
                    @foreach($photosAvant as $photo)
                        @php
                            $path = storage_path('app/public/' . $photo->chemin);
                            $b64 = file_exists($path) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path)) : null;
                        @endphp
                        @if($b64)
                            <div class="photo-card">
                                <img src="{{ $b64 }}">
                                <div class="photo-caption">{{ $photo->description ?? 'Vue Avant' }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($photosApres->isNotEmpty())
                <div class="photo-category-title">📸 Photos APRÈS Intervention (Travaux Finalisés)</div>
                <div>
                    @foreach($photosApres as $photo)
                        @php
                            $path = storage_path('app/public/' . $photo->chemin);
                            $b64 = file_exists($path) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path)) : null;
                        @endphp
                        @if($b64)
                            <div class="photo-card">
                                <img src="{{ $b64 }}">
                                <div class="photo-caption">{{ $photo->description ?? 'Vue Après' }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            @if($photosProbleme->isNotEmpty())
                <div class="photo-category-title">⚠️ Photos Problèmes & Constats Spécifiques</div>
                <div>
                    @foreach($photosProbleme as $photo)
                        @php
                            $path = storage_path('app/public/' . $photo->chemin);
                            $b64 = file_exists($path) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path)) : null;
                        @endphp
                        @if($b64)
                            <div class="photo-card">
                                <img src="{{ $b64 }}">
                                <div class="photo-caption">{{ $photo->description ?? 'Constat Terrain' }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- 5. QR Code Équipement & Localisation GPS -->
    <div class="section">
        <div class="section-header">5. Géolocalisation GPS & Traçabilité Équipement</div>
        <table class="grid-cards">
            <tr>
                <td style="width: 50%;">
                    <div class="card-box">
                        <div class="card-label">QR Code / Identifiant Équipement Scanné</div>
                        <div class="card-value" style="font-family: monospace; color: #4338ca;">
                            {{ $rapport->qrcode_scanne ?: ($rapport->intervention->emplacement->qr_code ?? 'Non scanné / Saisie directe') }}
                        </div>
                    </div>
                </td>
                <td style="width: 50%;">
                    <div class="card-box">
                        <div class="card-label">Coordonnées GPS d'Intervention</div>
                        <div class="card-value">
                            @if($rapport->gps_latitude && $rapport->gps_longitude)
                                Lat: {{ number_format($rapport->gps_latitude, 5) }}, Long: {{ number_format($rapport->gps_longitude, 5) }}
                                @if($rapport->gps_adresse)
                                    <br><span style="font-size: 8px; font-weight: normal; color: #475569;">{{ $rapport->gps_adresse }}</span>
                                @endif
                            @else
                                Position enregistrée via session de suivi mobile
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 6. Matériaux Consommés -->
    @if($rapport->intervention->materiaux && $rapport->intervention->materiaux->isNotEmpty())
        <div class="section">
            <div class="section-header">6. Matériaux & Pièces Consommées sur le Terrain</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation du Matériau</th>
                        <th style="text-align: center;">Quantité</th>
                        <th style="text-align: right;">Prix Unitaire HT</th>
                        <th style="text-align: right;">Total HT</th>
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
                            <td style="text-align: center; font-weight: bold;">{{ $item->quantite }} {{ $item->unite }}</td>
                            <td style="text-align: right;">{{ number_format($pu, 2, ',', ' ') }} MAD</td>
                            <td style="text-align: right; font-weight: bold;">{{ number_format($st, 2, ',', ' ') }} MAD</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="4" style="text-align: right; padding: 6px;">TOTAL MATÉRIAUX HT :</td>
                        <td style="text-align: right; color: {{ $cColor }}; font-size: 10px; padding: 6px;">{{ number_format($totalGeneral, 2, ',', ' ') }} MAD</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- 7. Visas & Signatures Réciproques -->
    <table class="signatures-table">
        <tr>
            <td class="sig-cell" style="margin-right: 4%;">
                <div class="sig-title">Visa & Signature du Technicien</div>
                @if($rapport->signature_technicien)
                    @php
                        $techPath = storage_path('app/public/' . $rapport->signature_technicien);
                        $techB64 = file_exists($techPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($techPath)) : null;
                    @endphp
                    @if($techB64)
                        <img src="{{ $techB64 }}" class="sig-img">
                    @else
                        <div style="font-family: monospace; color: #4338ca; font-weight: bold;">{{ $rapport->signature_technicien }}</div>
                    @endif
                @else
                    <div style="color: #dc2626; font-style: italic; margin-top: 15px; font-weight: bold;">Signature non renseignée</div>
                @endif
                <div style="font-size: 7.5px; color: #64748b; margin-top: 4px;">
                    Technicien : {{ $rapport->intervention->technicien->name ?? 'N/A' }}
                </div>
            </td>
            <td class="sig-cell">
                <div class="sig-title">Visa & Signature du Client</div>
                @if($rapport->signature_client)
                    @php
                        $clientPath = storage_path('app/public/' . $rapport->signature_client);
                        $clientB64 = file_exists($clientPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($clientPath)) : null;
                    @endphp
                    @if($clientB64)
                        <img src="{{ $clientB64 }}" class="sig-img">
                    @else
                        <div style="font-family: monospace; color: #4338ca; font-weight: bold;">{{ $rapport->signature_client }}</div>
                    @endif
                @else
                    <div style="color: #94a3b8; font-style: italic; margin-top: 15px;">Accusé de réception client digital</div>
                @endif
                <div style="font-size: 7.5px; color: #64748b; margin-top: 4px;">
                    Représentant Client : {{ $rapport->intervention->chantier->client->nom ?? 'Client' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer Fixe sur toutes les pages -->
    <div class="pdf-footer">
        <strong>{{ $cName }}</strong> — {{ $cAddress }}<br>
        Tél : {{ $cPhone }} • Fax : {{ $cFax }} • Email : {{ $cEmail }} • Web : {{ $cWebsite }}<br>
        ICE : {{ $cIce }} • RC : {{ $cRc }} • Document généré automatiquement par la plateforme d'Intervention Technique
    </div>

</body>
</html>
