<x-commercial-layout>
    <x-slot name="header">Détail Prospect</x-slot>

    @if(session('success'))
        <div class="kt-alert kt-alert-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg, rgba(62,151,255,0.15), rgba(62,151,255,0.08)); color:#3e97ff; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800;">
                {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
            </div>
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#181c32; margin-bottom:4px;">{{ $prospect->nom_entreprise }}</h2>
                <div style="display:flex; align-items:center; gap:8px;">
                    @php
                        $sColor = match($prospect->statut) {
                            'Nouveau'     => 'kt-badge-primary',
                            'Qualifié'    => 'kt-badge-info',
                            'Négociation' => 'kt-badge-warning',
                            'Converti'    => 'kt-badge-success',
                            'Perdu'       => 'kt-badge-danger',
                            default       => 'kt-badge-gray'
                        };
                    @endphp
                    <span class="kt-badge {{ $sColor }}">{{ $prospect->statut }}</span>
                    @if($prospect->commercial)
                        <span style="font-size:12px; color:#a1a5b7;">Commercial : {{ $prospect->commercial->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('prospects.index') }}" class="kt-btn kt-btn-light">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
            <a href="{{ route('prospects.edit', $prospect) }}" class="kt-btn kt-btn-light-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            @if($prospect->statut !== 'Converti')
                <a href="{{ route('commercial.devis.create', ['prospect_id' => $prospect->id]) }}" class="kt-btn kt-btn-primary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Créer un Devis
                </a>
                <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline" onsubmit="return confirm('Convertir ce prospect en client ?');">
                    @csrf
                    <button type="submit" class="kt-btn kt-btn-success">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Convertir en Client
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Content Grid -->
    <div style="display:grid; grid-template-columns: 1fr 1.6fr; gap:20px; align-items:start;">

        <!-- Left: Info Card -->
        <div>
            <div class="kt-card" style="margin-bottom:20px;">
                <div class="kt-card-header">
                    <div class="kt-card-title">Informations générales</div>
                </div>
                <div class="kt-card-body">
                    <div style="display:grid; gap:16px;">
                        <div>
                            <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Entreprise</div>
                            <div style="font-size:14px; font-weight:600; color:#181c32;">{{ $prospect->nom_entreprise }}</div>
                        </div>
                        <hr class="kt-separator">
                        <div>
                            <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Contact principal</div>
                            <div style="font-size:13px; color:#3f4254; font-weight:500;">{{ $prospect->nom_contact ?? '—' }}</div>
                        </div>
                        <hr class="kt-separator">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Email</div>
                                <div style="font-size:13px; color:#3e97ff; font-weight:500; word-break:break-all;">{{ $prospect->email ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Téléphone</div>
                                <div style="font-size:13px; color:#3f4254; font-weight:500;">{{ $prospect->telephone ?? '—' }}</div>
                            </div>
                        </div>
                        <hr class="kt-separator">
                        <div>
                            <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Adresse</div>
                            <div style="font-size:13px; color:#3f4254;">{{ $prospect->adresse ?? '—' }}</div>
                        </div>
                        @if($prospect->observations)
                            <hr class="kt-separator">
                            <div>
                                <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:6px;">Observations</div>
                                <div style="font-size:13px; color:#3f4254; background:#f5f8fa; border-radius:8px; padding:12px 14px; line-height:1.6;">{{ $prospect->observations }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Historique -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <div class="kt-card-title">Historique des actions</div>
                </div>
                <div class="kt-card-body" style="padding:16px 20px; max-height:300px; overflow-y:auto;">
                    @if(!empty($prospect->historique))
                        <div style="position:relative; padding-left:20px;">
                            <div style="position:absolute; left:7px; top:0; bottom:0; width:1px; background:#eff2f5;"></div>
                            @foreach(array_reverse($prospect->historique) as $hist)
                                <div style="position:relative; margin-bottom:16px; padding-bottom:16px; border-bottom:1px dashed #f5f5f5;">
                                    <div style="position:absolute; left:-17px; top:4px; width:10px; height:10px; border-radius:50%; background:white; border:2px solid #3e97ff;"></div>
                                    <div style="font-size:13px; font-weight:600; color:#181c32; margin-bottom:2px;">{{ $hist['action'] }}</div>
                                    <div style="font-size:11px; color:#a1a5b7;">Par {{ $hist['user'] }} · {{ \Carbon\Carbon::parse($hist['date'])->format('d/m/Y H:i') }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="kt-empty-state" style="padding:30px 16px;">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>Aucun historique disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Notes -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Notes & Échanges</div>
            </div>

            <!-- Add Note Form -->
            <div style="padding:16px 20px; border-bottom:1px dashed #eff2f5; background:#f9f9f9;">
                <form action="{{ route('prospects.notes.store', $prospect) }}" method="POST">
                    @csrf
                    <div class="kt-form-group" style="margin-bottom:10px;">
                        <textarea name="content" required rows="3" placeholder="Ajouter un commentaire, échange ou rappel..." class="kt-form-control" style="resize:none;"></textarea>
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" class="kt-btn kt-btn-primary kt-btn-sm">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Ajouter la note
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notes List -->
            <div style="max-height:500px; overflow-y:auto; padding:16px 20px;">
                @if(!empty($prospect->notes))
                    <div style="display:grid; gap:12px;">
                        @foreach(array_reverse($prospect->notes) as $note)
                            <div style="background:#f5f8fa; border-radius:10px; padding:14px 16px; border-left:3px solid #3e97ff;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <div style="width:28px; height:28px; border-radius:50%; background:#3e97ff; color:white; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0;">
                                            {{ strtoupper(substr($note['user'], 0, 2)) }}
                                        </div>
                                        <span style="font-size:12px; font-weight:700; color:#181c32;">{{ $note['user'] }}</span>
                                    </div>
                                    <span style="font-size:11px; color:#a1a5b7;">{{ \Carbon\Carbon::parse($note['date'])->format('d/m/Y H:i') }}</span>
                                </div>
                                <p style="font-size:13px; color:#3f4254; line-height:1.6; margin:0;">{{ $note['content'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="kt-empty-state" style="padding:40px 16px;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p>Aucune note pour le moment</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-commercial-layout>
