<x-client-layout>
    <x-slot name="header">Consulter le Rapport</x-slot>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h2 style="font-size:20px; font-weight:800; color:#181c32; margin-bottom:2px;">
                Rapport : {{ $rapport->intervention?->code_intervention ?? 'Rapport #'.$rapport->id }}
            </h2>
            <div style="font-size:12px; color:#a1a5b7;">
                Généré le {{ $rapport->created_at->format('d/m/Y H:i') }} pour le chantier "{{ $rapport->intervention?->chantier?->nom }}"
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('client.rapports.index') }}" class="kt-btn kt-btn-light">
                Retour
            </a>
            <a href="{{ route('client.rapports.pdf', $rapport) }}" target="_blank" class="kt-btn kt-btn-success">
                Télécharger PDF
            </a>
        </div>
    </div>

    <!-- Rapport Content Card -->
    <div class="kt-card" style="margin-bottom:24px;">
        <div class="kt-card-header">
            <div class="kt-card-title">Résumé du Rapport</div>
        </div>
        <div class="kt-card-body">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; margin-bottom:20px;">
                <div>
                    <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Technicien</div>
                    <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $rapport->intervention?->technicien?->name ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Durée Réelle</div>
                    <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $rapport->intervention?->duree_reelle ?? '—' }} min</div>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Date Clôture</div>
                    <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $rapport->created_at->format('d/m/Y') }}</div>
                </div>
            </div>

            <hr class="kt-separator" style="margin-bottom:20px;">

            <div>
                <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:6px;">Commentaire / Travaux Réalisés</div>
                <div style="font-size:13px; color:#3f4254; background:#f5f8fa; padding:16px; border-radius:10px; line-height:1.6;">
                    {{ $rapport->commentaire ?? 'Aucun commentaire.' }}
                </div>
            </div>

            @if($rapport->signature)
                <div style="margin-top:20px;">
                    <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:6px;">Signature</div>
                    <img src="{{ Storage::url($rapport->signature) }}" alt="Signature" style="max-height:100px; border:1px solid #eff2f5; border-radius:8px; padding:6px;">
                </div>
            @endif
        </div>
    </div>

    <!-- Photos Section -->
    @if($rapport->photos->isNotEmpty())
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Photos liées à l'intervention ({{ $rapport->photos->count() }})</div>
            </div>
            <div class="kt-card-body">
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:16px;">
                    @foreach($rapport->photos as $photo)
                        <div style="border:1px solid #eff2f5; border-radius:10px; overflow:hidden;">
                            <img src="{{ Storage::url($photo->chemin) }}" alt="Photo" style="width:100%; height:160px; object-fit:cover;">
                            @if($photo->description)
                                <div style="padding:8px 12px; font-size:11px; color:#5e6278; background:#f9f9f9;">{{ $photo->description }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</x-client-layout>
