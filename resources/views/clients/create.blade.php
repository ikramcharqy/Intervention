<x-app-layout>
    <x-slot name="header">
        {{ __('Nouveau Client') }}
    </x-slot>

    <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" x-data="{ typeClient: '{{ old('type_client', 'Entreprise') }}' }">
        @csrf

        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('clients.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour
            </a>
            <div class="flex gap-2">
                <a href="{{ route('clients.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Annuler</a>
                <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Enregistrer</button>
            </div>
        </div>

        <div class="ds-card-elevated">
            <!-- Section : Informations client -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Informations client</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Identité principale et coordonnées du client.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="code_client" class="ds-label">Code Client <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="code_client" id="code_client" required value="{{ old('code_client') }}" placeholder="Ex: CLI-2026-0001" class="ds-input">
                        @error('code_client') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="type_client" class="ds-label">Type de Client <span style="color:#F0142F;">*</span></label>
                        <select name="type_client" id="type_client" required x-model="typeClient" class="ds-input">
                            <option value="Entreprise">Entreprise</option>
                            <option value="Particulier">Particulier</option>
                            <option value="Administration">Administration</option>
                        </select>
                        @error('type_client') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="nom" class="ds-label"><span x-text="typeClient === 'Particulier' ? 'Nom complet' : 'Nom ou Raison Sociale'"></span> <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="nom" id="nom" required value="{{ old('nom') }}" class="ds-input">
                        @error('nom') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="nom_contact" class="ds-label">Nom du Contact</label>
                        <input type="text" name="nom_contact" id="nom_contact" value="{{ old('nom_contact') }}" class="ds-input">
                        @error('nom_contact') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    @if(!auth()->user()->hasRole('Commercial'))
                        <div>
                            <label for="commercial_id" class="ds-label">Commercial Responsable</label>
                            <select name="commercial_id" id="commercial_id" class="ds-input">
                                <option value="">Aucun (à assigner plus tard)</option>
                                @foreach($commerciaux as $com)
                                    <option value="{{ $com->id }}" @selected(old('commercial_id') == $com->id)>
                                        {{ $com->prenom }} {{ $com->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('commercial_id') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    <div>
                        <label for="email" class="ds-label">E-mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="ds-input">
                        @error('email') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="telephone" class="ds-label">Téléphone principal <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="telephone" id="telephone" required value="{{ old('telephone') }}" class="ds-input">
                        @error('telephone') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="telephone_secondaire" class="ds-label">Téléphone secondaire</label>
                        <input type="text" name="telephone_secondaire" id="telephone_secondaire" value="{{ old('telephone_secondaire') }}" class="ds-input">
                        @error('telephone_secondaire') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Adresse -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Adresse</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Informations de facturation et de localisation.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div class="md:col-span-2">
                        <label for="adresse_facturation" class="ds-label">Adresse de Facturation</label>
                        <textarea name="adresse_facturation" id="adresse_facturation" rows="3" class="ds-input" style="height:auto; padding-top:0.75rem;">{{ old('adresse_facturation') }}</textarea>
                        @error('adresse_facturation') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="ville" class="ds-label">Ville <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="ville" id="ville" required value="{{ old('ville') }}" class="ds-input">
                        @error('ville') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="pays" class="ds-label">Pays</label>
                        <input type="text" name="pays" id="pays" value="{{ old('pays', 'Maroc') }}" class="ds-input">
                        @error('pays') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Entreprise (conditionnelle) -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;" x-show="typeClient === 'Entreprise'">
                <h3 class="text-[16px] font-bold text-[#131523]">Informations de l'Entreprise</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Identifiants légaux (visibles uniquement pour un client de type Entreprise).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="ice" class="ds-label">ICE <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="ice" id="ice" :required="typeClient === 'Entreprise'" value="{{ old('ice') }}" class="ds-input">
                        @error('ice') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="if" class="ds-label">Identifiant Fiscal (IF)</label>
                        <input type="text" name="if" id="if" value="{{ old('if') }}" class="ds-input">
                        @error('if') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="rc" class="ds-label">Registre du Commerce (RC)</label>
                        <input type="text" name="rc" id="rc" value="{{ old('rc') }}" class="ds-input">
                        @error('rc') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="patente" class="ds-label">Patente</label>
                        <input type="text" name="patente" id="patente" value="{{ old('patente') }}" class="ds-input">
                        @error('patente') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Identité (conditionnelle, client Particulier) -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;" x-show="typeClient === 'Particulier'">
                <h3 class="text-[16px] font-bold text-[#131523]">Identité du Client</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Donnée personnelle sensible (loi 09-08) : visible uniquement par l'Admin et le Commercial assigné.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="numero_cin" class="ds-label">Numéro CIN <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="numero_cin" id="numero_cin" :required="typeClient === 'Particulier'" value="{{ old('numero_cin') }}" placeholder="Ex: AB123456" class="ds-input">
                        @error('numero_cin') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="date_naissance" class="ds-label">Date de Naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance" value="{{ old('date_naissance') }}" class="ds-input">
                        @error('date_naissance') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cin_recto" class="ds-label">Scan CIN Recto</label>
                        <input type="file" name="cin_recto" id="cin_recto" accept=".pdf,.jpg,.jpeg,.png" class="ds-input">
                        @error('cin_recto') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="cin_verso" class="ds-label">Scan CIN Verso</label>
                        <input type="file" name="cin_verso" id="cin_verso" accept=".pdf,.jpg,.jpeg,.png" class="ds-input">
                        @error('cin_verso') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Observations -->
            <div class="p-7">
                <h3 class="text-[16px] font-bold text-[#131523]">Observations</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Notes ou précisions complémentaires sur ce client.</p>

                <div class="mt-5">
                    <label for="observations" class="ds-label">Observations</label>
                    <textarea name="observations" id="observations" rows="4" class="ds-input" style="height:auto; padding-top:0.75rem;">{{ old('observations') }}</textarea>
                    @error('observations') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-5">
            <a href="{{ route('clients.index') }}" class="ds-btn ds-btn-white ds-btn-md">Annuler</a>
            <button type="submit" class="ds-btn ds-btn-primary ds-btn-md">Enregistrer</button>
        </div>
    </form>
</x-app-layout>
