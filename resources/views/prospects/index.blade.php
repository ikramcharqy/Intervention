@php
    $layout = auth()->user()->hasRole('Commercial') ? 'commercial-layout' : (auth()->user()->hasRole('Super Admin') ? 'superadmin-layout' : 'app-layout');
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">Prospects</x-slot>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-3 text-sm font-semibold">
            <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <h2 class="kt-card-title">Liste des Prospects</h2>
                <div class="text-xs text-slate-400 mt-0.5 font-medium">{{ $prospects->count() }} prospect(s) trouvé(s)</div>
            </div>
            <a href="{{ route('prospects.create') }}" class="kt-btn kt-btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouveau Prospect
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Contact</th>
                        <th>Téléphone</th>
                        <th>Commercial</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prospects as $prospect)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="kt-avatar bg-emerald-500/10 text-emerald-600">
                                        {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-xs">{{ $prospect->nom_entreprise }}</div>
                                        <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $prospect->adresse ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-700 text-xs">{{ $prospect->nom_contact }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $prospect->email }}</div>
                            </td>
                            <td>
                                <span class="text-xs font-mono text-slate-600">{{ $prospect->telephone ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600 font-medium">{{ $prospect->commercial ? $prospect->commercial->name : 'Non assigné' }}</span>
                            </td>
                            <td>
                                @php
                                    $pColor = match($prospect->statut) {
                                        'Nouveau' => 'kt-badge-info',
                                        'En cours' => 'kt-badge-warning',
                                        'Converti' => 'kt-badge-success',
                                        'Perdu' => 'kt-badge-danger',
                                        default => 'kt-badge-gray'
                                    };
                                @endphp
                                <span class="kt-badge {{ $pColor }}">{{ $prospect->statut }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('prospects.show', $prospect) }}" class="kt-btn kt-btn-light kt-btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('prospects.edit', $prospect) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </a>
                                    @if($prospect->statut !== 'Converti')
                                        <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline" onsubmit="return confirm('Convertir ce prospect en client ?');">
                                            @csrf
                                            <button type="submit" class="kt-btn kt-btn-light-success kt-btn-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Convertir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kt-empty-state">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="text-sm text-slate-500 font-medium">Aucun prospect enregistré pour le moment.</p>
                                    <a href="{{ route('prospects.create') }}" class="kt-btn kt-btn-primary mt-3">Créer votre premier prospect</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dynamic-component>
