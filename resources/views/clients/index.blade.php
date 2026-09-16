<x-app-layout>
    <x-slot name="header">
        {{ __('Portefeuille Clients') }}
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="ds-card-elevated p-4 text-sm font-medium" style="background-color:#C4F8E2; color:#06A561;">
                {!! session('success') !!}
            </div>
        @endif

        @if($nonAssignesCount > 0)
            <a href="{{ route('clients.non-assignes') }}" class="ds-card-elevated p-4 flex items-center justify-between hover:opacity-90 transition" style="background-color:#FFF4C9;">
                <span class="text-sm font-semibold inline-flex items-center gap-2" style="color:#F99600;">
                    <i class="ti ti-alert-triangle"></i>
                    {{ $nonAssignesCount }} client(s) sans commercial assigné
                </span>
                <span class="text-xs font-semibold" style="color:#F99600;">Traiter →</span>
            </a>
        @endif

        <!-- Outils et filtres -->
        <div class="ds-card-elevated p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom, code, email, téléphone..."
                        class="ds-input w-full md:w-72" style="height:40px;">
                    @if($commerciaux->isNotEmpty())
                        <select name="commercial_id" class="ds-input w-full md:w-56" style="height:40px;">
                            <option value="">Tous les commerciaux</option>
                            @foreach($commerciaux as $com)
                                <option value="{{ $com->id }}" @selected(($commercialFilter ?? '') == $com->id)>
                                    {{ $com->prenom }} {{ $com->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Filtrer</button>
                    @if($search || $commercialFilter)
                        <a href="{{ route('clients.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Réinitialiser</a>
                    @endif
                </form>
                @can('create', \App\Models\Client::class)
                    <a href="{{ route('clients.create') }}" class="ds-btn ds-btn-primary ds-btn-sm w-full md:w-auto">
                        <i class="ti ti-plus"></i>
                        Ajouter un Client
                    </a>
                @endcan
            </div>
        </div>

        <!-- Table des clients -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Ville</th>
                            <th>Commercial</th>
                            <th>Statut</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr class="{{ !$client->is_active ? 'opacity-60' : '' }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background-color:#D9E4FF;">
                                            <span class="text-xs font-bold" style="color:#1E5EFF;">{{ strtoupper(mb_substr($client->nom, 0, 1)) }}</span>
                                        </div>
                                        <span class="font-semibold text-[#131523]">{{ $client->nom }}</span>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-[#5A607F]">{{ $client->code_client }}</td>
                                <td class="text-xs text-[#5A607F]">
                                    <span class="ds-badge ds-badge-sm ds-badge-light-secondary">{{ $client->type_client }}</span>
                                </td>
                                <td class="text-xs text-[#5A607F]">{{ $client->ville }}</td>
                                <td>
                                    @if($client->commercial)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0" style="background-color:#C4F8E2;">
                                                <span class="text-[9px] font-bold" style="color:#06A561;">{{ strtoupper(mb_substr($client->commercial->prenom ?? $client->commercial->name, 0, 1).mb_substr($client->commercial->name, 0, 1)) }}</span>
                                            </div>
                                            <span class="text-xs font-semibold text-[#5A607F]">{{ $client->commercial->prenom }} {{ $client->commercial->name }}</span>
                                        </div>
                                    @else
                                        <span class="ds-badge ds-badge-sm ds-badge-light-warning">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $client->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                                        {{ $client->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('clients.show', $client) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Voir</a>
                                        @can('update', $client)
                                            <a href="{{ route('clients.edit', $client) }}" class="text-xs font-semibold text-[#5A607F] hover:text-[#131523]">Modifier</a>
                                        @endcan
                                        @can('delete', $client)
                                            @if ($client->is_active)
                                                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Désactiver ce client ?');" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Désactiver</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('clients.restore', $client) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-semibold" style="color:#06A561;">Réactiver</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="text-center py-14">
                                        <i class="ti ti-users text-3xl mb-3 block" style="color:#D7DBEC;"></i>
                                        <p class="text-sm font-medium" style="color:#A1A7C4;">Aucun client enregistré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:#E6E9F4;">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
