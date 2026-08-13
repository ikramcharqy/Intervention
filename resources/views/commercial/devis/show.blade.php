<x-commercial-layout>
    <x-slot name="header">Détail Devis</x-slot>

    @if(session('success'))
        <div class="kt-alert kt-alert-success" style="margin-bottom:20px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:52px; height:52px; border-radius:12px; background:rgba(114,57,234,0.12); color:#7239ea; display:flex; align-items:center; justify-content:center;">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#181c32; margin-bottom:4px;">{{ $devis->reference ?? 'DEVIS-'.$devis->id }}</h2>
                <div style="display:flex; align-items:center; gap:8px;">
                    @php
                        $dColor = match($devis->statut) {
                            'Brouillon' => 'kt-badge-gray',
                            'Envoyé'    => 'kt-badge-warning',
                            'Accepté'   => 'kt-badge-success',
                            'Refusé'    => 'kt-badge-danger',
                            default     => 'kt-badge-gray'
                        };
                    @endphp
                    <span class="kt-badge {{ $dColor }}">{{ $devis->statut }}</span>
                    @if($devis->date_emission)
                        <span style="font-size:12px; color:#a1a5b7;">Émis le {{ $devis->date_emission->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('commercial.devis.index') }}" class="kt-btn kt-btn-light">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
            <a href="{{ route('commercial.devis.edit', $devis) }}" class="kt-btn kt-btn-light-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <form action="{{ route('commercial.devis.duplicate', $devis) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="kt-btn kt-btn-light-success">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Dupliquer
                </button>
            </form>
            <a href="{{ route('commercial.devis.pdf', $devis) }}" target="_blank" class="kt-btn kt-btn-danger">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Imprimer PDF
            </a>
            <form action="{{ route('commercial.devis.destroy', $devis) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce devis ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="kt-btn kt-btn-light-danger">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Content -->
    <div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

        <!-- Main Devis Card -->
        <div class="kt-card">
            <!-- Emetteur & Destinataire -->
            <div class="kt-card-body" style="padding-bottom:16px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-bottom:24px;">
                    <!-- Emetteur -->
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:1px; margin-bottom:10px;">Émetteur</div>
                        <div style="font-size:15px; font-weight:800; color:#181c32; margin-bottom:4px;">Intervention CRM</div>
                        <div style="font-size:12px; color:#a1a5b7; margin-top:2px;">Commercial : {{ $devis->commercial ? $devis->commercial->name : 'N/A' }}</div>
                    </div>

                    <!-- Destinataire -->
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:1px; margin-bottom:10px;">Destinataire</div>
                        @if($devis->prospect)
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                                <span class="kt-badge kt-badge-primary" style="font-size:9px;">Prospect</span>
                            </div>
                            <div style="font-size:15px; font-weight:800; color:#181c32; margin-bottom:4px;">{{ $devis->prospect->nom_entreprise }}</div>
                            <div style="font-size:12px; color:#5e6278; margin-top:3px;">{{ $devis->prospect->nom_contact }}</div>
                            <div style="font-size:12px; color:#a1a5b7; margin-top:2px;">{{ $devis->prospect->email }}</div>
                            <div style="font-size:12px; color:#a1a5b7;">{{ $devis->prospect->telephone }}</div>
                        @elseif($devis->client)
                            <div style="display:flex; align-items:center; gap:6px; margin-bottom:6px;">
                                <span class="kt-badge kt-badge-success" style="font-size:9px;">Client</span>
                            </div>
                            <div style="font-size:15px; font-weight:800; color:#181c32; margin-bottom:4px;">{{ $devis->client->nom }}</div>
                            <div style="font-size:12px; color:#5e6278; margin-top:3px;">{{ $devis->client->nom_contact ?? '' }}</div>
                            <div style="font-size:12px; color:#a1a5b7; margin-top:2px;">{{ $devis->client->email ?? '' }}</div>
                            <div style="font-size:12px; color:#a1a5b7;">{{ $devis->client->telephone ?? '' }}</div>
                        @else
                            <div style="font-size:13px; color:#a1a5b7;">Non spécifié</div>
                        @endif
                    </div>
                </div>

                <!-- Info Strips -->
                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:12px; background:#f5f8fa; border-radius:10px; padding:16px;">
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:4px;">Statut</div>
                        <span class="kt-badge {{ $dColor }}" style="font-size:11px;">{{ $devis->statut }}</span>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:4px;">Date émission</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32;">{{ $devis->date_emission ? $devis->date_emission->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:4px;">Date expiration</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32;">{{ $devis->date_expiration ? $devis->date_expiration->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:4px;">Taux TVA</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32;">{{ $devis->taux_tva }}%</div>
                    </div>
                </div>
            </div>

            <hr class="kt-separator">

            <!-- Lignes Table -->
            <div class="kt-card-body">
                <div style="font-size:13px; font-weight:700; color:#181c32; margin-bottom:14px;">Détail des prestations</div>
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Description</th>
                            <th style="text-align:center;">Qté</th>
                            <th style="text-align:right;">Prix Unit. HT</th>
                            <th style="text-align:right;">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devis->lignes as $ligne)
                            <tr>
                                <td style="font-weight:600; color:#181c32;">{{ $ligne->designation }}</td>
                                <td style="color:#7e8299; font-size:12px;">{{ $ligne->description ?? '—' }}</td>
                                <td style="text-align:center; font-weight:500;">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                <td style="text-align:right; font-weight:500;">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
                                <td style="text-align:right; font-weight:700; color:#181c32;">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($devis->observations)
                <hr class="kt-separator">
                <div class="kt-card-body" style="padding-top:16px;">
                    <div style="background:rgba(255,199,0,0.06); border:1px solid rgba(255,199,0,0.2); border-radius:10px; padding:14px 16px;">
                        <div style="font-size:11px; font-weight:700; color:#e9b500; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">Observations</div>
                        <p style="font-size:13px; color:#5e6278; line-height:1.6; margin:0; white-space:pre-wrap;">{{ $devis->observations }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar: Totaux -->
        <div>
            <div class="kt-card">
                <div class="kt-card-header" style="min-height:50px;">
                    <div class="kt-card-title">Récapitulatif</div>
                </div>
                <div class="kt-card-body">
                    <div style="display:grid; gap:14px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:13px; color:#7e8299;">Sous-total HT</span>
                            <span style="font-size:13px; font-weight:600; color:#181c32;">{{ number_format($devis->montant_ht, 2, ',', ' ') }} €</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:13px; color:#7e8299;">TVA ({{ $devis->taux_tva }}%)</span>
                            <span style="font-size:13px; font-weight:600; color:#181c32;">{{ number_format($devis->montant_tva, 2, ',', ' ') }} €</span>
                        </div>
                        <hr class="kt-separator">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:14px; font-weight:700; color:#181c32;">Total TTC</span>
                            <span style="font-size:18px; font-weight:800; color:#3e97ff;">{{ number_format($devis->montant_ttc, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="kt-card" style="margin-top:16px;">
                <div class="kt-card-header" style="min-height:50px;">
                    <div class="kt-card-title">Actions rapides</div>
                </div>
                <div class="kt-card-body" style="display:grid; gap:8px;">
                    <a href="{{ route('commercial.devis.pdf', $devis) }}" target="_blank" class="kt-btn kt-btn-danger" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Télécharger PDF
                    </a>
                    <a href="{{ route('commercial.devis.edit', $devis) }}" class="kt-btn kt-btn-light-primary" style="width:100%; justify-content:center;">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier le devis
                    </a>
                    <form action="{{ route('commercial.devis.duplicate', $devis) }}" method="POST">
                        @csrf
                        <button type="submit" class="kt-btn kt-btn-light-success" style="width:100%; justify-content:center;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Dupliquer ce devis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-commercial-layout>
