<x-app-layout>
    <x-slot name="header">
        {{ __('Fiche Chantier : ') }} {{ $chantier->nom }}
    </x-slot>

    @php
        $typeLocalIcons = [
            'Bureau' => 'ti-building', 'Maison' => 'ti-home', 'Appartement' => 'ti-building-skyscraper',
            'Magasin' => 'ti-building-store', 'Usine' => 'ti-building-factory-2', 'Restaurant' => 'ti-tools-kitchen-2',
            'Hotel' => 'ti-building-lighthouse', 'Hopital' => 'ti-first-aid-kit', 'Ecole' => 'ti-school',
            'Administration' => 'ti-building-bank', 'Entrepôt' => 'ti-building-warehouse',
            'Local technique' => 'ti-tool', 'Site industriel' => 'ti-building-factory-2',
            'Commerce' => 'ti-building-store', 'Société' => 'ti-building-skyscraper', 'Entreprise' => 'ti-building-skyscraper',
        ];
    @endphp

    <div>
        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('chantiers.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour aux chantiers
            </a>
            <div class="flex gap-2">
                <a href="{{ route('chantiers.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Fermer</a>
                <a href="{{ route('chantiers.edit', $chantier) }}" class="ds-btn ds-btn-primary ds-btn-sm">Modifier</a>
            </div>
        </div>

        @if (session('success'))
            <div class="ds-card-elevated p-4 mb-5 text-sm font-medium" style="background-color:#C4F8E2; color:#06A561;">
                {!! session('success') !!}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte profil du chantier -->
                <div class="ds-card-elevated p-7">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0" style="background-color:#D9E4FF;">
                                <i class="ti {{ $typeLocalIcons[$chantier->type_local] ?? 'ti-building' }}" style="color:#1E5EFF; font-size:26px;"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-[#131523]">{{ $chantier->nom }}</h2>
                                <p class="text-xs text-[#5A607F] mt-0.5">{{ $chantier->type_local }} &middot; {{ $chantier->ville }}</p>
                                <p class="text-xs text-[#A1A7C4] mt-1">{{ $chantier->code_chantier }}</p>
                            </div>
                        </div>
                        <span class="ds-badge ds-badge-sm {{ $chantier->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                            {{ $chantier->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    <div class="border-t mt-6 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm" style="border-color:#E6E9F4;">
                        <div><span class="text-[#A1A7C4]">Client :</span> <a href="{{ route('clients.show', $chantier->client) }}" class="text-[#131523] font-medium hover:underline">{{ $chantier->client->nom ?? '-' }}</a></div>
                        <div><span class="text-[#A1A7C4]">Ville :</span> <span class="text-[#131523] font-medium">{{ $chantier->ville }}</span></div>
                        <div class="md:col-span-2"><span class="text-[#A1A7C4]">Adresse :</span> <span class="text-[#131523] font-medium">{{ $chantier->adresse }}</span></div>
                        @if($chantier->latitude && $chantier->longitude)
                            <div class="md:col-span-2"><span class="text-[#A1A7C4]">Coordonnées GPS :</span> <span class="text-[#131523] font-medium">{{ $chantier->latitude }}, {{ $chantier->longitude }}</span></div>
                        @endif
                    </div>

                    @if($chantier->description)
                        <div class="border-t mt-6 pt-5" style="border-color:#E6E9F4;">
                            <h4 class="text-sm font-bold text-[#131523] mb-1">Description</h4>
                            <p class="text-sm text-[#5A607F] whitespace-pre-wrap">{{ $chantier->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Emplacements (Nested Component) -->
                @include('chantiers.partials.emplacements-table')

                <!-- Interventions (groupées par emplacement si applicable) -->
                @include('chantiers.partials.interventions-list')
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <div class="ds-card-elevated p-7">
                    <h3 class="text-[16px] font-bold text-[#131523] mb-4">Contact responsable</h3>

                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Responsable</dt>
                            <dd class="text-[#131523] font-medium mt-0.5">{{ $chantier->responsable }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Téléphone</dt>
                            <dd class="text-[#5A607F] mt-0.5">{{ $chantier->telephone_responsable }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">E-mail</dt>
                            <dd class="text-[#5A607F] mt-0.5">{{ $chantier->email_responsable }}</dd>
                        </div>
                    </dl>

                    <div class="border-t mt-5 pt-4 flex flex-col gap-2" style="border-color:#E6E9F4;">
                        @if ($chantier->is_active)
                            <form method="POST" action="{{ route('chantiers.destroy', $chantier) }}" onsubmit="return confirm('Désactiver ce chantier ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Désactiver le chantier</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('chantiers.restore', $chantier) }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold" style="color:#06A561;">Réactiver le chantier</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="ds-card-elevated p-7">
                    <h3 class="text-[16px] font-bold text-[#131523] mb-1">Étiquettes</h3>
                    <p class="text-[11px] text-[#A1A7C4] mb-4">Type de local et ville.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                            {{ $chantier->type_local }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                            {{ $chantier->ville }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
