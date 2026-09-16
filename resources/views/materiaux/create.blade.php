<x-app-layout>
    <x-slot name="header">
        {{ __('Nouveau Matériau') }}
    </x-slot>

    <form method="POST" action="{{ route('materiaux.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('materiaux.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour
            </a>
            <div class="flex gap-2">
                <a href="{{ route('materiaux.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Annuler</a>
                <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Enregistrer</button>
            </div>
        </div>

        <div class="ds-card-elevated">
            <!-- Section : Informations -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Informations</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Informations principales sur le matériau.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="nom" class="ds-label">Nom du matériau <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="nom" id="nom" required value="{{ old('nom') }}" class="ds-input">
                        @error('nom') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="reference" class="ds-label">Référence (auto-générée si vide)</label>
                        <input type="text" name="reference" id="reference" value="{{ old('reference') }}" placeholder="Ex: CBL-RJ45-001" class="ds-input">
                        @error('reference') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="categorie" class="ds-label">Catégorie <span style="color:#F0142F;">*</span></label>
                        <select name="categorie" id="categorie" required class="ds-input">
                            <option value="">Sélectionner...</option>
                            @foreach(\App\Models\Materiau::CATEGORIES as $cat)
                                <option value="{{ $cat }}" @selected(old('categorie') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('categorie') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="unite" class="ds-label">Unité de mesure <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="unite" id="unite" required value="{{ old('unite') }}" placeholder="Ex: m, pièce, boîte, rouleau" class="ds-input">
                        @error('unite') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="marque" class="ds-label">Marque</label>
                        <input type="text" name="marque" id="marque" value="{{ old('marque') }}" placeholder="Ex: Ubiquiti, TP-Link, Cisco" class="ds-input">
                        @error('marque') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="modele_fabricant" class="ds-label">Modèle fabricant</label>
                        <input type="text" name="modele_fabricant" id="modele_fabricant" value="{{ old('modele_fabricant') }}" placeholder="Référence constructeur" class="ds-input">
                        @error('modele_fabricant') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2 flex items-center gap-2 pt-1">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true)) class="rounded">
                        <label for="is_active" class="text-sm font-medium text-[#131523]">Actif</label>
                    </div>
                </div>
            </div>

            <!-- Section : Image & Fiche technique -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Image &amp; Fiche technique</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Visuel du matériau et documentation fabricant (optionnel).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="image" class="ds-label">Image</label>
                        <label for="image" class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed cursor-pointer p-6 text-center transition hover:opacity-80" style="border-color:#D9E1EC; background-color:#F5F6FA;">
                            <i class="ti ti-cloud-upload text-2xl" style="color:#A1A7C4;"></i>
                            <span class="text-xs font-medium text-[#131523]">Cliquez pour choisir une image</span>
                            <span class="text-[11px] text-[#A1A7C4]">PNG, JPG jusqu'à 4 Mo</span>
                            <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="document.getElementById('image-filename').textContent = this.files[0]?.name ?? ''">
                        </label>
                        <p id="image-filename" class="text-xs font-medium mt-2" style="color:#1E5EFF;"></p>
                        @error('image') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="fiche_technique" class="ds-label">Fiche technique (PDF)</label>
                        <label for="fiche_technique" class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed cursor-pointer p-6 text-center transition hover:opacity-80" style="border-color:#D9E1EC; background-color:#F5F6FA;">
                            <i class="ti ti-file-type-pdf text-2xl" style="color:#A1A7C4;"></i>
                            <span class="text-xs font-medium text-[#131523]">Cliquez pour choisir un PDF</span>
                            <span class="text-[11px] text-[#A1A7C4]">Datasheet fabricant, jusqu'à 8 Mo</span>
                            <input type="file" name="fiche_technique" id="fiche_technique" accept="application/pdf" class="hidden" onchange="document.getElementById('fiche-filename').textContent = this.files[0]?.name ?? ''">
                        </label>
                        <p id="fiche-filename" class="text-xs font-medium mt-2" style="color:#1E5EFF;"></p>
                        @error('fiche_technique') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Prix & Stock -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Prix &amp; Stock</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Tarification et disponibilité initiale.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                    <div>
                        <label for="prix_unitaire" class="ds-label">Prix unitaire HT (DH) <span style="color:#F0142F;">*</span></label>
                        <input type="number" name="prix_unitaire" id="prix_unitaire" required value="{{ old('prix_unitaire') }}" step="0.01" min="0.01" class="ds-input">
                        @error('prix_unitaire') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="stock" class="ds-label">Stock initial <span style="color:#F0142F;">*</span></label>
                        <input type="number" name="stock" id="stock" required value="{{ old('stock', 0) }}" step="0.01" min="0" class="ds-input">
                        @error('stock') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="seuil_alerte" class="ds-label">Seuil d'alerte stock bas</label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" value="{{ old('seuil_alerte', 5) }}" step="0.01" min="0" class="ds-input">
                        @error('seuil_alerte') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Description -->
            <div class="p-7">
                <h3 class="text-[16px] font-bold text-[#131523]">Description</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Notes ou précisions complémentaires sur le matériau.</p>

                <div class="mt-5">
                    <label for="description" class="ds-label">Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Ajouter des précisions sur ce matériau..." class="ds-input" style="height:auto; padding-top:0.75rem;">{{ old('description') }}</textarea>
                    @error('description') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-5">
            <a href="{{ route('materiaux.index') }}" class="ds-btn ds-btn-white ds-btn-md">Annuler</a>
            <button type="submit" class="ds-btn ds-btn-primary ds-btn-md">Enregistrer</button>
        </div>
    </form>
</x-app-layout>
