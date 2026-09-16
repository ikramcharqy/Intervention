<x-app-layout>
    <x-slot name="header">
        {{ __('Clients à assigner') }}
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="ds-card-elevated p-4 text-sm font-medium" style="background-color:#C4F8E2; color:#06A561;">
                {!! session('success') !!}
            </div>
        @endif

        <div class="ds-card-elevated p-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#131523]">Clients sans commercial assigné</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Ces clients ne sont suivis par aucun commercial — assignez-les pour garantir leur suivi.</p>
            </div>
            <a href="{{ route('clients.index') }}" class="ds-btn ds-btn-white ds-btn-sm">
                <i class="ti ti-arrow-left"></i> Retour au portefeuille
            </a>
        </div>

        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Code</th>
                            <th>Ville</th>
                            <th>Créé le</th>
                            <th>Assigner à</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0" style="background-color:#D9E4FF;">
                                            <span class="text-xs font-bold" style="color:#1E5EFF;">{{ strtoupper(mb_substr($client->nom, 0, 1)) }}</span>
                                        </div>
                                        <span class="font-semibold text-[#131523]">{{ $client->nom }}</span>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-[#5A607F]">{{ $client->code_client }}</td>
                                <td class="text-xs text-[#5A607F]">{{ $client->ville }}</td>
                                <td class="text-xs text-[#A1A7C4]">{{ $client->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('clients.reassign', $client) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="from" value="non-assignes">
                                        <select name="nouveau_commercial_id" required class="ds-input" style="height:36px; min-width:180px;">
                                            <option value="">Choisir...</option>
                                            @foreach($commerciaux as $com)
                                                <option value="{{ $com->id }}">{{ $com->prenom }} {{ $com->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm shrink-0">Assigner</button>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('clients.show', $client) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="text-center py-14">
                                        <i class="ti ti-circle-check text-3xl mb-3 block" style="color:#1FD286;"></i>
                                        <p class="text-sm font-medium" style="color:#A1A7C4;">Tous les clients ont un commercial assigné.</p>
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
