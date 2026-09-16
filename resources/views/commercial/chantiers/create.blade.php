<x-commercial-layout>
    <x-slot name="header">
        {{ __('Nouveau Chantier') }}
    </x-slot>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @php
        $typeLocalCartes = [
            'Bureau'          => 'ti-building',
            'Entrepôt'        => 'ti-building-warehouse',
            'Site industriel' => 'ti-building-factory-2',
            'Commerce'        => 'ti-building-store',
        ];
    @endphp

    <form method="POST" action="{{ route('chantiers.store') }}">
        @csrf

        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('chantiers.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour
            </a>
            <div class="flex gap-2">
                <a href="{{ route('chantiers.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Annuler</a>
                <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Enregistrer</button>
            </div>
        </div>

        <div class="ds-card-elevated">
            <!-- Section : Informations du chantier -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Informations du chantier</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Le code sera utilisé pour identifier le chantier dans les interventions.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div>
                        <label for="client_search" class="ds-label">Client <span style="color:#F0142F;">*</span></label>
                        <input type="text" id="client_search" list="clients-datalist" autocomplete="off"
                            placeholder="Rechercher un client par nom ou code..." class="ds-input">
                        <datalist id="clients-datalist">
                            @foreach ($clients as $client)
                                <option data-id="{{ $client->id }}" value="{{ $client->nom }} ({{ $client->code_client }})"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="client_id" id="client_id" required value="{{ old('client_id', request('client_id')) }}">
                        @error('client_id') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="code_chantier" class="ds-label">Code Chantier (auto-généré si vide)</label>
                        <input type="text" name="code_chantier" id="code_chantier" value="{{ old('code_chantier') }}"
                            placeholder="Ex: CHT-CLI001-001" class="ds-input">
                        @error('code_chantier') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="nom" class="ds-label">Nom du chantier <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="nom" id="nom" required value="{{ old('nom') }}" class="ds-input">
                        @error('nom') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Type de chantier -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Type de chantier</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Nature du local sur lequel les interventions seront réalisées.</p>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-5" id="type-local-cards">
                    @foreach ($typeLocalCartes as $type => $icon)
                        <button type="button" data-type="{{ $type }}"
                            class="type-local-card rounded-xl border p-4 text-center transition hover:opacity-80"
                            style="border-color:#D9E1EC;">
                            <i class="ti {{ $icon }} text-xl block mb-2" style="color:#5A607F;"></i>
                            <span class="text-xs font-semibold text-[#131523]">{{ $type }}</span>
                        </button>
                    @endforeach
                    <button type="button" data-type="__autre__"
                        class="type-local-card rounded-xl border p-4 text-center transition hover:opacity-80"
                        style="border-color:#D9E1EC;">
                        <i class="ti ti-dots text-xl block mb-2" style="color:#5A607F;"></i>
                        <span class="text-xs font-semibold text-[#131523]">Autre...</span>
                    </button>
                </div>

                <div id="type-local-autre-wrapper" class="mt-4" style="display:none">
                    <label for="type_local_autre" class="ds-label">Préciser le type</label>
                    <select id="type_local_autre" class="ds-input">
                        <option value="">-- Sélectionner --</option>
                        @foreach (\App\Models\Chantier::TYPES_LOCAL as $type)
                            <option value="{{ $type }}" @selected(old('type_local') == $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="type_local" id="type_local" required value="{{ old('type_local') }}">
                @error('type_local') <p class="text-xs mt-2" style="color:#F0142F;">{{ $message }}</p> @enderror
            </div>

            <!-- Section : Localisation -->
            <div class="p-7 border-b" style="border-color:#E6E9F4;">
                <h3 class="text-[16px] font-bold text-[#131523]">Localisation</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Adresse et positionnement GPS du chantier (coordonnées optionnelles).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div class="md:col-span-2">
                        <label for="adresse" class="ds-label">Adresse <span style="color:#F0142F;">*</span></label>
                        <textarea name="adresse" id="adresse" required rows="2" class="ds-input" style="height:auto; padding-top:0.75rem;">{{ old('adresse', request('adresse')) }}</textarea>
                        @error('adresse') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="ville_select" class="ds-label">Ville <span style="color:#F0142F;">*</span></label>
                        <select id="ville_select" class="ds-input">
                            <option value="">-- Sélectionner --</option>
                            @foreach (\App\Models\Chantier::VILLES_PRINCIPALES as $villePrincipale)
                                <option value="{{ $villePrincipale }}">{{ $villePrincipale }}</option>
                            @endforeach
                            <option value="Autre">Autre...</option>
                        </select>
                        <input type="text" id="ville_autre" placeholder="Précisez la ville" style="display:none" class="ds-input mt-2">
                        <input type="hidden" name="ville" id="ville" required value="{{ old('ville') }}">
                        @error('ville') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <div id="chantier-map" style="height:250px; border-color:#D9E1EC;" class="rounded-xl border"></div>
                        <p class="text-[11px] text-[#A1A7C4] mt-2">Cliquez sur la carte pour positionner précisément le chantier.</p>
                    </div>
                    <div>
                        <label for="latitude" class="ds-label">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude') }}" class="ds-input">
                        @error('latitude') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="longitude" class="ds-label">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude') }}" class="ds-input">
                        @error('longitude') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section : Contact responsable -->
            <div class="p-7">
                <h3 class="text-[16px] font-bold text-[#131523]">Contact responsable</h3>
                <p class="text-xs text-[#A1A7C4] mt-0.5">Interlocuteur terrain à contacter pour ce chantier.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                    <div class="md:col-span-2">
                        <label for="responsable" class="ds-label">Responsable <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="responsable" id="responsable" required value="{{ old('responsable') }}" class="ds-input">
                        @error('responsable') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="telephone_responsable" class="ds-label">Téléphone responsable <span style="color:#F0142F;">*</span></label>
                        <input type="text" name="telephone_responsable" id="telephone_responsable" required value="{{ old('telephone_responsable') }}" class="ds-input">
                        @error('telephone_responsable') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email_responsable" class="ds-label">E-mail responsable <span style="color:#F0142F;">*</span></label>
                        <input type="email" name="email_responsable" id="email_responsable" required value="{{ old('email_responsable') }}" class="ds-input">
                        @error('email_responsable') <span class="text-xs" style="color:#F0142F;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-5">
            <a href="{{ route('chantiers.index') }}" class="ds-btn ds-btn-white ds-btn-md">Annuler</a>
            <button type="submit" class="ds-btn ds-btn-primary ds-btn-md">Enregistrer</button>
        </div>
    </form>

    <script>
        const clientSearch = document.getElementById('client_search');
        const clientIdInput = document.getElementById('client_id');
        const clientsDatalist = document.getElementById('clients-datalist');

        function syncClientId() {
            const match = Array.from(clientsDatalist.options).find(opt => opt.value === clientSearch.value);
            clientIdInput.value = match ? match.dataset.id : '';
        }

        clientSearch.addEventListener('input', syncClientId);

        // Pré-remplissage si un client_id est déjà connu (old() ou ?client_id= depuis la fiche Client)
        if (clientIdInput.value) {
            const preselected = Array.from(clientsDatalist.options).find(opt => opt.dataset.id === String(clientIdInput.value));
            if (preselected) {
                clientSearch.value = preselected.value;
            }
        }

        // Type de chantier : cartes sélectionnables + repli "Autre" en select complet
        const typeLocalHidden = document.getElementById('type_local');
        const typeLocalCards = document.querySelectorAll('.type-local-card');
        const typeLocalAutreWrapper = document.getElementById('type-local-autre-wrapper');
        const typeLocalAutreSelect = document.getElementById('type_local_autre');

        function selectTypeLocalCard(card, silent) {
            typeLocalCards.forEach(c => {
                c.style.borderColor = '#D9E1EC';
                c.style.backgroundColor = '';
                c.querySelector('i').style.color = '#5A607F';
            });
            card.style.borderColor = '#1E5EFF';
            card.style.backgroundColor = '#D9E4FF';
            card.querySelector('i').style.color = '#1E5EFF';

            if (card.dataset.type === '__autre__') {
                typeLocalAutreWrapper.style.display = '';
                typeLocalHidden.value = typeLocalAutreSelect.value;
            } else {
                typeLocalAutreWrapper.style.display = 'none';
                typeLocalHidden.value = card.dataset.type;
            }
        }

        typeLocalCards.forEach(card => {
            card.addEventListener('click', () => selectTypeLocalCard(card, false));
        });

        typeLocalAutreSelect.addEventListener('change', () => {
            typeLocalHidden.value = typeLocalAutreSelect.value;
        });

        // Pré-sélection depuis old() (retour de validation)
        if (typeLocalHidden.value) {
            const matchingCard = Array.from(typeLocalCards).find(c => c.dataset.type === typeLocalHidden.value);
            if (matchingCard) {
                selectTypeLocalCard(matchingCard);
            } else {
                const autreCard = Array.from(typeLocalCards).find(c => c.dataset.type === '__autre__');
                selectTypeLocalCard(autreCard);
                typeLocalAutreSelect.value = typeLocalHidden.value;
            }
        }

        // Ville : select principal + repli "Autre" en texte libre
        const villeSelect = document.getElementById('ville_select');
        const villeAutre = document.getElementById('ville_autre');
        const villeHidden = document.getElementById('ville');

        function syncVille() {
            if (villeSelect.value === 'Autre') {
                villeAutre.style.display = '';
                villeHidden.value = villeAutre.value;
            } else {
                villeAutre.style.display = 'none';
                villeHidden.value = villeSelect.value;
            }
        }

        villeSelect.addEventListener('change', syncVille);
        villeAutre.addEventListener('input', () => { villeHidden.value = villeAutre.value; });

        // Pré-remplissage de la ville depuis old() (retour de validation)
        if (villeHidden.value) {
            const known = Array.from(villeSelect.options).find(opt => opt.value === villeHidden.value);
            if (known) {
                villeSelect.value = villeHidden.value;
            } else {
                villeSelect.value = 'Autre';
                villeAutre.value = villeHidden.value;
                villeAutre.style.display = '';
            }
        }

        // Mini-carte Leaflet : clic pour positionner le chantier
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const initialLat = parseFloat(latInput.value) || 33.5731; // Casablanca par défaut
        const initialLng = parseFloat(lngInput.value) || -7.5898;

        const chantierMap = L.map('chantier-map').setView([initialLat, initialLng], latInput.value ? 14 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(chantierMap);

        let chantierMarker = null;
        if (latInput.value && lngInput.value) {
            chantierMarker = L.marker([initialLat, initialLng]).addTo(chantierMap);
        }

        chantierMap.on('click', function (e) {
            const { lat, lng } = e.latlng;
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);

            if (chantierMarker) {
                chantierMarker.setLatLng(e.latlng);
            } else {
                chantierMarker = L.marker(e.latlng).addTo(chantierMap);
            }
        });
    </script>
</x-commercial-layout>
