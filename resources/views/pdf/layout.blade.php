<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentTitle ?? 'Document' }}{{ isset($documentReference) ? ' — '.$documentReference : '' }}</title>
    <style>
        @page {
            margin: 34px 36px 62px 36px;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
        }

        /* En-tête de marque — commun à tous les documents générés par la plateforme */
        .brand-header {
            width: 100%;
            border-bottom: 3px solid {{ $company['color'] ?? '#10b981' }};
            padding-bottom: 12px;
            margin-bottom: 22px;
        }
        .brand-header table { width: 100%; border-collapse: collapse; }
        .brand-name {
            font-size: 19px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand-tagline { font-size: 8.5px; color: #64748b; font-weight: 600; margin-top: 1px; }
        .doc-title {
            font-size: 14px;
            font-weight: 800;
            color: {{ $company['color'] ?? '#10b981' }};
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .doc-ref {
            display: inline-block;
            padding: 2px 8px;
            background-color: #ecfdf5;
            color: #047857;
            font-size: 9.5px;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 4px;
        }
        .meta-right { text-align: right; font-size: 9.5px; color: #64748b; vertical-align: top; }
        .meta-right strong { color: #0f172a; }

        /* Sections communes, réutilisables par tous les templates enfants */
        .section { margin-bottom: 16px; page-break-inside: avoid; }
        .section-header {
            font-size: 10.5px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            background-color: #f1f5f9;
            padding: 6px 10px;
            border-left: 4px solid {{ $company['color'] ?? '#10b981' }};
            margin-bottom: 9px;
            border-radius: 0 4px 4px 0;
        }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
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
        .data-table tr:nth-child(even) td { background-color: #f8fafc; }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 8px 10px;
        }

        /* Pied de page fixe avec pagination native dompdf (compteurs CSS, sans PHP embarqué) */
        .pdf-footer {
            position: fixed;
            bottom: -48px;
            left: 0;
            right: 0;
            height: 42px;
            font-size: 7.5px;
            color: #64748b;
            border-top: 1px solid #cbd5e1;
            padding-top: 5px;
            text-align: center;
            line-height: 1.4;
        }
        .pdf-footer strong { color: #1e293b; }
        .page-number:after { content: "Page " counter(page) " / " counter(pages); }

        @stack('styles')
    </style>
</head>
<body>

    <div class="brand-header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @if(!empty($company['logo']) && file_exists($company['logo']))
                        <img src="{{ $company['logo'] }}" style="max-height: 44px; max-width: 180px; margin-bottom: 3px;">
                    @else
                        <div class="brand-name">{{ $company['name'] ?? 'TechniTrack' }}</div>
                    @endif
                    <div class="brand-tagline">{{ $company['tagline'] ?? "Excellence en Maintenance & Interventions Techniques" }}</div>
                    <div class="doc-title">{{ $documentTitle ?? 'Document' }}</div>
                    @if(!empty($documentReference))
                        <div class="doc-ref">Réf : {{ $documentReference }}</div>
                    @endif
                </td>
                <td class="meta-right">
                    <strong>Édité le :</strong> {{ now()->format('d/m/Y H:i') }}<br>
                    @yield('meta')
                </td>
            </tr>
        </table>
    </div>

    @yield('content')

    <div class="pdf-footer">
        <strong>{{ $company['name'] ?? 'TechniTrack' }}</strong>
        @if(!empty($company['address'])) — {{ $company['address'] }}@endif
        <br>
        @if(!empty($company['phone']))Tél : {{ $company['phone'] }} &bull; @endif
        @if(!empty($company['email']))Email : {{ $company['email'] }} &bull; @endif
        Document généré automatiquement par la plateforme TechniTrack
        <br>
        <span class="page-number"></span>
    </div>

</body>
</html>
