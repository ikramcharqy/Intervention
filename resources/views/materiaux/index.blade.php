<x-app-layout>
    <x-slot name="header">
        {{ __('Catalogue des Matériaux') }}
    </x-slot>

    @php
        $categorieIcons = [
            'Câblage'            => 'ti-plug-connected',
            'Équipement réseau'  => 'ti-router',
            'Vidéosurveillance'  => 'ti-camera',
            'Connectique'        => 'ti-plug',
            'Outillage'          => 'ti-tool',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Outils et filtres -->
        <div class="ds-card-elevated p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('materiaux.index') }}" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom, référence ou description..."
                        class="ds-input w-full md:w-80" style="height:40px;">
                    <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">
                        Rechercher
                    </button>
                    @if($search)
                        <a href="{{ route('materiaux.index') }}" class="ds-btn ds-btn-white ds-btn-sm">
                            Réinitialiser
                        </a>
                    @endif
                </form>
                @if($canSeePrix)
                    <a href="{{ route('materiaux.create') }}" class="ds-btn ds-btn-primary ds-btn-sm w-full md:w-auto">
                        <i class="ti ti-plus"></i>
                        Nouveau Matériau
                    </a>
                @endif
            </div>
        </div>

        <!-- Table des matériaux -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            @if($canSeePrix)
                                <th class="w-10"><input type="checkbox" class="rounded" onclick="document.querySelectorAll('.materiau-row-check').forEach(c => c.checked = this.checked)"></th>
                            @endif
                            <th>Matériau</th>
                            <th>Référence</th>
                            <th>Catégorie</th>
                            <th>Unité</th>
                            @if($canSeePrix)
                                <th>Prix unitaire</th>
                                <th>Stock</th>
                            @else
                                <th>Disponibilité</th>
                            @endif
                            <th>Statut</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materiaux as $materiau)
                            <tr class="{{ !$materiau->is_active ? 'opacity-60' : '' }}">
                                @if($canSeePrix)
                                    <td><input type="checkbox" class="materiau-row-check rounded" value="{{ $materiau->id }}"></td>
                                @endif
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if($materiau->image_path)
                                            <img src="{{ Storage::url($materiau->image_path) }}" alt="{{ $materiau->nom }}" class="w-10 h-10 rounded-lg object-cover border" style="border-color:#E6E9F4;">
                                        @else
                                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0" style="background-color:#F1F4FA;">
                                                <i class="ti {{ $categorieIcons[$materiau->categorie] ?? 'ti-package' }}" style="color:#1E5EFF; font-size:18px;"></i>
                                            </div>
                                        @endif
                                        <span class="font-semibold text-[#131523]">{{ $materiau->nom }}</span>
                                    </div>
                                </td>
                                <td class="font-mono text-xs text-[#5A607F]">
                                    {{ $materiau->reference ?? '-' }}
                                </td>
                                <td class="text-xs text-[#5A607F]">
                                    {{ $materiau->categorie ?? '-' }}
                                </td>
                                <td class="text-xs text-[#5A607F]">
                                    {{ $materiau->unite }}
                                </td>
                                @if($canSeePrix)
                                    <td class="font-medium text-[#131523]">
                                        @if ($materiau->prix_unitaire !== null && $materiau->prix_unitaire > 0)
                                            {{ number_format($materiau->prix_unitaire, 2, ',', ' ') }} DH
                                        @else
                                            <span class="text-[#A1A7C4]">À renseigner</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-[#131523]">{{ number_format($materiau->stock, 2) }}</span>
                                            @if($materiau->estEnStockBas())
                                                <span class="ds-badge ds-badge-sm ds-badge-light-danger">Stock bas</span>
                                            @else
                                                <span class="ds-badge ds-badge-sm ds-badge-light-success">En stock</span>
                                            @endif
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        @if($materiau->estEnStockBas())
                                            <span class="ds-badge ds-badge-sm ds-badge-light-danger">Stock bas</span>
                                        @elseif($materiau->stock > 0)
                                            <span class="ds-badge ds-badge-sm ds-badge-light-success">Disponible</span>
                                        @else
                                            <span class="ds-badge ds-badge-sm ds-badge-light-danger">Rupture</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $materiau->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                                        {{ $materiau->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('materiaux.show', $materiau) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Voir</a>
                                        @if($canSeePrix)
                                            <a href="{{ route('materiaux.edit', $materiau) }}" class="text-xs font-semibold text-[#5A607F] hover:text-[#131523]">Modifier</a>
                                        @endif
                                        @if(auth()->user()?->hasRole('Super Admin'))
                                            <form method="POST" action="{{ route('materiaux.destroy', $materiau) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce matériau ?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="text-center py-14">
                                        <i class="ti ti-package text-3xl mb-3 block" style="color:#D7DBEC;"></i>
                                        <p class="text-sm font-medium" style="color:#A1A7C4;">Aucun matériau enregistré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($materiaux->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:#E6E9F4;">
                    {{ $materiaux->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
