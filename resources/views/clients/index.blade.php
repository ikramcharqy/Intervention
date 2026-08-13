@php
    $layout = auth()->user()->hasRole('Commercial') ? 'commercial-layout' : (auth()->user()->hasRole('Super Admin') ? 'superadmin-layout' : 'app-layout');
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">Portefeuille Clients</x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="kt-card p-5">
            <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col md:flex-row gap-3 items-end flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom, code, email, téléphone..."
                        class="w-full rounded-lg border-slate-200 text-xs px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                @if($commerciaux->isNotEmpty())
                <div class="min-w-[200px]">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Commercial</label>
                    <select name="commercial_id" class="w-full rounded-lg border-slate-200 text-xs px-3.5 py-2.5">
                        <option value="">Tous les commerciaux</option>
                        @foreach($commerciaux as $com)
                            <option value="{{ $com->id }}" @selected(($commercialFilter ?? '') == $com->id)>
                                {{ $com->prenom }} {{ $com->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="flex gap-2">
                    <button type="submit" class="kt-btn kt-btn-primary">
                        Filtrer
                    </button>
                    @if($search || $commercialFilter)
                        <a href="{{ route('clients.index') }}" class="kt-btn kt-btn-light">
                            Réinitialiser
                        </a>
                    @endif
                </div>

                <div class="md:ml-auto">
                    <a href="{{ route('clients.create') }}" class="kt-btn kt-btn-success">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Nouveau Client
                    </a>
                </div>
            </form>
        </div>

        {{-- Notifications --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {!! session('success') !!}
            </div>
        @endif

        {{-- Table --}}
        <div class="kt-card">
            <div class="overflow-x-auto">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Nom / Raison Sociale</th>
                            <th>Type</th>
                            <th>Téléphone</th>
                            <th>Ville</th>
                            <th>Commercial</th>
                            <th>Statut</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr class="{{ !$client->is_active ? 'opacity-50' : '' }}">
                                <td class="font-mono font-bold text-xs text-slate-500">
                                    {{ $client->code_client }}
                                </td>
                                <td>
                                    <div class="font-bold text-slate-800 text-xs">{{ $client->nom }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $client->email }}</div>
                                </td>
                                <td>
                                    <span class="kt-badge kt-badge-info">
                                        {{ $client->type_client }}
                                    </span>
                                </td>
                                <td class="font-mono text-xs">{{ $client->telephone ?? '—' }}</td>
                                <td class="text-xs font-medium text-slate-600">{{ $client->ville ?? '—' }}</td>
                                <td>
                                    @if($client->commercial)
                                        <div class="flex items-center gap-2">
                                            <div class="kt-avatar bg-emerald-500/10 text-emerald-600">
                                                {{ strtoupper(substr($client->commercial->prenom ?? '', 0, 1) . substr($client->commercial->name ?? '', 0, 1)) }}
                                            </div>
                                            <span class="text-xs font-semibold text-slate-700">
                                                {{ $client->commercial->prenom }} {{ $client->commercial->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="kt-badge {{ $client->is_active ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                        {{ $client->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('clients.show', $client) }}" class="kt-btn kt-btn-light kt-btn-sm">Voir</a>
                                        <a href="{{ route('clients.edit', $client) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">Modifier</a>
                                        @if ($client->is_active)
                                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Désactiver ce client ?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="kt-btn kt-btn-light-danger kt-btn-sm">Désactiver</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('clients.restore', $client) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="kt-btn kt-btn-light-success kt-btn-sm">Réactiver</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="kt-empty-state">
                                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p class="text-sm text-slate-500 font-medium">Aucun client enregistré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
