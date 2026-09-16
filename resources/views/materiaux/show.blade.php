<x-app-layout>
    <x-slot name="header">
        {{ __('Détails du Matériau : ') }} {{ $materiau->nom }}
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

    <div>
        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('materiaux.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour au catalogue
            </a>
            <div class="flex gap-2">
                <a href="{{ route('materiaux.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Fermer</a>
                @if($canSeePrix)
                    <a href="{{ route('materiaux.edit', $materiau) }}" class="ds-btn ds-btn-primary ds-btn-sm">Modifier</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte profil du matériau -->
                <div class="ds-card-elevated p-7">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @if($materiau->image_path)
                                <img src="{{ Storage::url($materiau->image_path) }}" alt="{{ $materiau->nom }}" class="w-16 h-16 rounded-full object-cover border shrink-0" style="border-color:#E6E9F4;">
                            @else
                                <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0" style="background-color:#F1F4FA;">
                                    <i class="ti {{ $categorieIcons[$materiau->categorie] ?? 'ti-package' }}" style="color:#1E5EFF; font-size:26px;"></i>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-bold text-[#131523]">{{ $materiau->nom }}</h2>
                                <p class="text-xs text-[#5A607F] mt-0.5">{{ $materiau->categorie ?? 'Sans catégorie' }}@if($materiau->marque) &middot; {{ $materiau->marque }}@endif</p>
                                <p class="text-xs text-[#A1A7C4] mt-1">
                                    {{ $materiau->reference }} &middot; {{ $materiau->interventions->count() }} utilisation(s) enregistrée(s)
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            <span class="ds-badge ds-badge-sm {{ $materiau->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                                {{ $materiau->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            @if($materiau->estEnStockBas())
                                <span class="ds-badge ds-badge-sm ds-badge-light-danger">Stock bas</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t mt-6 pt-5" style="border-color:#E6E9F4;">
                        <h4 class="text-sm font-bold text-[#131523] mb-1">Description</h4>
                        <p class="text-[11px] text-[#A1A7C4] mb-2">Notes et précisions sur ce matériau.</p>
                        <div class="rounded-lg border p-3 text-sm text-[#5A607F] min-h-[60px]" style="border-color:#D9E1EC; background-color:#F5F6FA;">
                            {{ $materiau->description ?: 'Aucune description renseignée.' }}
                        </div>
                    </div>
                </div>

                @if($canSeePrix)
                    <div class="ds-card-elevated p-7">
                        <h3 class="text-[16px] font-bold text-[#131523] mb-4">Réapprovisionner le stock</h3>
                        <form method="POST" action="{{ route('materiaux.reapprovisionner', $materiau) }}" class="flex flex-col md:flex-row gap-3 items-end">
                            @csrf
                            <div class="flex-1 w-full">
                                <label for="quantite" class="ds-label">Quantité à ajouter ({{ $materiau->unite }})</label>
                                <input type="number" name="quantite" id="quantite" required step="0.01" min="0.01" class="ds-input">
                            </div>
                            <div class="flex-1 w-full">
                                <label for="commentaire" class="ds-label">Commentaire (optionnel)</label>
                                <input type="text" name="commentaire" id="commentaire" placeholder="Ex: Achat fournisseur X" class="ds-input">
                            </div>
                            <button type="submit" class="ds-btn ds-btn-primary ds-btn-md shrink-0">Réapprovisionner</button>
                        </form>
                        @error('quantite') <p class="text-xs mt-2" style="color:#F0142F;">{{ $message }}</p> @enderror
                    </div>
                @endif

                @if($canSeePrix)
                    <div class="ds-card-elevated overflow-hidden">
                        <div class="p-7 pb-0 flex items-center justify-between">
                            <h3 class="text-[16px] font-bold text-[#131523]">Historique des mouvements récents</h3>
                            <a href="{{ route('materiaux.mouvements', $materiau) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Voir tout l'historique →</a>
                        </div>
                        <div class="overflow-x-auto mt-5">
                            <table class="ds-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Quantité</th>
                                        <th>Stock après</th>
                                        <th>Intervention</th>
                                        <th>Technicien</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($materiau->mouvements()->with(['intervention', 'technicien'])->latest()->limit(5)->get() as $mvt)
                                        <tr>
                                            <td>
                                                <span class="ds-badge ds-badge-sm {{ $mvt->type_mouvement === 'entree' ? 'ds-badge-light-success' : 'ds-badge-light-secondary' }}">
                                                    {{ $mvt->type_mouvement === 'entree' ? 'Entrée' : 'Sortie' }}
                                                </span>
                                            </td>
                                            <td class="font-semibold text-[#131523]">{{ number_format($mvt->quantite, 2) }} {{ $materiau->unite }}</td>
                                            <td class="text-[#5A607F]">{{ number_format($mvt->stock_apres, 2) }}</td>
                                            <td>
                                                @if($mvt->intervention)
                                                    <a href="{{ route('interventions.show', $mvt->intervention) }}" class="text-xs font-semibold" style="color:#1E5EFF;">{{ $mvt->intervention->code_intervention }}</a>
                                                @else
                                                    <span class="text-[#A1A7C4]">-</span>
                                                @endif
                                            </td>
                                            <td class="text-[#5A607F]">{{ $mvt->technicien->name ?? '-' }}</td>
                                            <td class="text-[#A1A7C4] text-xs">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-8 text-xs text-[#A1A7C4]">Aucun mouvement de stock enregistré pour ce matériau.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="ds-card-elevated overflow-hidden">
                    <div class="p-7 pb-0">
                        <h3 class="text-[16px] font-bold text-[#131523]">Historique des utilisations</h3>
                    </div>
                    <div class="overflow-x-auto mt-5">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Intervention</th>
                                    <th>Technicien</th>
                                    <th>Quantité</th>
                                    @if($canSeePrix)
                                        <th>Coût total</th>
                                    @endif
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($materiau->interventions as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('interventions.show', $item->intervention) }}" class="text-xs font-semibold" style="color:#1E5EFF;">
                                                {{ $item->intervention->code_intervention ?? '#'.$item->intervention_id }}
                                            </a>
                                        </td>
                                        <td class="text-[#5A607F]">{{ $item->intervention->technicien->name ?? 'N/A' }}</td>
                                        <td class="text-[#131523] font-medium">{{ $item->quantite }} {{ $item->unite ?? $materiau->unite }}</td>
                                        @if($canSeePrix)
                                            <td class="text-[#131523]">
                                                @if($materiau->prix_unitaire > 0)
                                                    {{ number_format($item->quantite * $materiau->prix_unitaire, 2, ',', ' ') }} DH
                                                @else
                                                    <span class="text-[#A1A7C4]">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <span class="ds-badge ds-badge-sm {{ $item->is_valide ? 'ds-badge-light-success' : 'ds-badge-light-warning' }}">
                                                {{ $item->is_valide ? 'Décompté du stock' : 'En attente de validation' }}
                                            </span>
                                        </td>
                                        <td class="text-[#A1A7C4] text-xs">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-[#5A607F] text-xs">{{ $item->commentaire ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-xs text-[#A1A7C4]">Aucune utilisation enregistrée pour ce matériau.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <div class="ds-card-elevated p-7">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[16px] font-bold text-[#131523]">Aperçu</h3>
                        @if($canSeePrix)
                            <a href="{{ route('materiaux.edit', $materiau) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Modifier</a>
                        @endif
                    </div>

                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Marque</dt>
                            <dd class="text-[#5A607F] mt-0.5">{{ $materiau->marque ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Modèle fabricant</dt>
                            <dd class="text-[#5A607F] mt-0.5">{{ $materiau->modele_fabricant ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">QR Code</dt>
                            <dd class="text-[#5A607F] mt-0.5 font-mono text-xs">{{ $materiau->qr_code ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Fiche technique</dt>
                            <dd class="mt-0.5">
                                @if($materiau->fiche_technique_path)
                                    <a href="{{ Storage::url($materiau->fiche_technique_path) }}" target="_blank" class="text-xs font-semibold inline-flex items-center gap-1" style="color:#1E5EFF;">
                                        <i class="ti ti-file-type-pdf"></i> Voir le PDF
                                    </a>
                                @else
                                    <span class="text-[#A1A7C4]">—</span>
                                @endif
                            </dd>
                        </div>
                        @if($canSeePrix)
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Prix unitaire</dt>
                                <dd class="text-[#5A607F] mt-0.5">
                                    @if($materiau->prix_unitaire > 0)
                                        {{ number_format($materiau->prix_unitaire, 2, ',', ' ') }} DH
                                    @else
                                        <span class="text-[#A1A7C4]">À renseigner</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Stock / Seuil d'alerte</dt>
                                <dd class="text-[#5A607F] mt-0.5">{{ number_format($materiau->stock, 2) }} / {{ number_format($materiau->seuil_alerte, 2) }} {{ $materiau->unite }}</dd>
                            </div>
                        @endif
                    </dl>

                    @if(auth()->user()?->hasRole('Super Admin'))
                        <div class="border-t mt-5 pt-4" style="border-color:#E6E9F4;">
                            <form method="POST" action="{{ route('materiaux.destroy', $materiau) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce matériau ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Supprimer le matériau</button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="ds-card-elevated p-7">
                    <h3 class="text-[16px] font-bold text-[#131523] mb-1">Étiquettes</h3>
                    <p class="text-[11px] text-[#A1A7C4] mb-4">Catégorie et statut de disponibilité.</p>
                    <div class="flex flex-wrap gap-2">
                        @if($materiau->categorie)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                                {{ $materiau->categorie }}
                            </span>
                        @endif
                        @if($materiau->marque)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                                {{ $materiau->marque }}
                            </span>
                        @endif
                        @if($materiau->estEnStockBas())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#FCD5D9; color:#F0142F;">
                                Stock bas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
