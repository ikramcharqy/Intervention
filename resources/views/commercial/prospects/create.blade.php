<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="w-full space-y-5">
        <!-- Barre du haut -->
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('prospects.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5 text-[#5E6278] hover:text-[#181C32]">
                    <i class="fas fa-arrow-left text-[10px]"></i> Prospects
                </a>
                <h1 class="text-lg font-extrabold text-[#181C32] font-heading mt-1">Créer un Prospect</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('prospects.index') }}" class="px-3.5 py-2 text-[#5E6278] text-xs font-bold hover:text-[#181C32] transition">Annuler</a>
                <button type="submit" form="prospect-form" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">Enregistrer</button>
            </div>
        </div>

        <form id="prospect-form" action="{{ route('prospects.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Informations du Prospect -->
            <div class="metronic-card p-6">
                <h2 class="text-sm font-bold text-[#181C32] font-heading">Informations du Prospect</h2>
                <p class="text-xs text-[#A1A5B7] mt-0.5 mb-5">Identité et coordonnées du prospect.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @if($commerciaux->isNotEmpty())
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#181C32] mb-1.5">Assigné à</label>
                            <select name="commercial_id" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="">Moi-même</option>
                                @foreach($commerciaux as $c)
                                    <option value="{{ $c->id }}" {{ old('commercial_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label id="label-nom-entreprise" class="block text-xs font-bold text-[#181C32] mb-1.5">Nom de l'entreprise <span class="text-rose-500">*</span></label>
                        <input type="text" name="nom_entreprise" required value="{{ old('nom_entreprise') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Ex: Acme Corp">
                        @error('nom_entreprise')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Nom du contact</label>
                        <input type="text" name="nom_contact" value="{{ old('nom_contact') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Ex: Jean Dupont">
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-[11px] text-[#A1A5B7]">Renseignez au moins un moyen de contact : téléphone ou e-mail.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="contact@entreprise.fr">
                        @error('email')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="0612345678">
                        @error('telephone')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Adresse complète">
                    </div>
                </div>
            </div>

            <!-- Type de Prospect -->
            <div class="metronic-card p-6">
                <h2 class="text-sm font-bold text-[#181C32] font-heading">Type de Prospect</h2>
                <p class="text-xs text-[#A1A5B7] mt-0.5 mb-5">Détermine le libellé du nom et le type de client à la conversion.</p>

                <div class="grid grid-cols-2 sm:w-1/2 gap-3" id="type-prospect-cards">
                    @php
                        $typeProspectIcons = ['Entreprise' => 'fa-building', 'Particulier' => 'fa-user'];
                        $selectedTypeProspect = old('type_prospect', 'Entreprise');
                    @endphp
                    @foreach(\App\Models\Prospect::TYPES_PROSPECT as $tp)
                        <button type="button" onclick="selectTypeProspect('{{ $tp }}')"
                                class="type-prospect-card flex flex-col items-center gap-2 px-4 py-4 rounded-xl border transition {{ $selectedTypeProspect === $tp ? 'border-emerald-500 bg-emerald-50' : 'border-[#EFF2F5] hover:border-emerald-200' }}"
                                data-value="{{ $tp }}">
                            <i class="fas {{ $typeProspectIcons[$tp] }} text-lg {{ $selectedTypeProspect === $tp ? 'text-emerald-600' : 'text-[#A1A5B7]' }}"></i>
                            <span class="text-xs font-bold {{ $selectedTypeProspect === $tp ? 'text-emerald-700' : 'text-[#5E6278]' }}">{{ $tp }}</span>
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="type_prospect" id="type_prospect" value="{{ $selectedTypeProspect }}" required>
            </div>

            <!-- Suivi -->
            <div class="metronic-card p-6">
                <h2 class="text-sm font-bold text-[#181C32] font-heading">Suivi Commercial</h2>
                <p class="text-xs text-[#A1A5B7] mt-0.5 mb-5">Statut actuel et prochaine relance programmée.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Statut</label>
                        <select name="statut" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            <option value="Nouveau" {{ old('statut') == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                            <option value="Qualifié" {{ old('statut') == 'Qualifié' ? 'selected' : '' }}>Qualifié</option>
                            <option value="Négociation" {{ old('statut') == 'Négociation' ? 'selected' : '' }}>Négociation</option>
                            <option value="Perdu" {{ old('statut') == 'Perdu' ? 'selected' : '' }}>Perdu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Prochaine action — date</label>
                        <input type="date" name="prochaine_action_date" value="{{ old('prochaine_action_date') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Prochaine action — description</label>
                        <input type="text" name="prochaine_action_description" value="{{ old('prochaine_action_description') }}" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Ex: Rappeler suite envoi devis">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#181C32] mb-2">Type(s) de besoin technique</label>
                        <div class="flex flex-wrap gap-2">
                            @forelse($typesIntervention as $type)
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-[#EFF2F5] text-xs font-semibold text-[#5E6278] cursor-pointer has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-200 has-[:checked]:text-emerald-700 transition">
                                    <input type="checkbox" name="type_intervention_ids[]" value="{{ $type->id }}"
                                           {{ in_array($type->id, old('type_intervention_ids', [])) ? 'checked' : '' }}
                                           class="rounded border-[#D9D9D9] text-emerald-600 focus:ring-emerald-500">
                                    {{ $type->nom }}
                                </label>
                            @empty
                                <p class="text-xs text-[#A1A5B7] italic">Aucun type d'intervention configuré.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[#181C32] mb-1.5">Observations</label>
                        <textarea name="observations" rows="4" class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Notes, contexte, besoins particuliers...">{{ old('observations') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('prospects.index') }}" class="px-3.5 py-2 text-[#5E6278] text-xs font-bold hover:text-[#181C32] transition">Annuler</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                    <i class="fas fa-check mr-1.5"></i> Enregistrer le prospect
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectTypeProspect(value) {
            document.getElementById('type_prospect').value = value;
            document.querySelectorAll('.type-prospect-card').forEach(card => {
                const active = card.dataset.value === value;
                card.classList.toggle('border-emerald-500', active);
                card.classList.toggle('bg-emerald-50', active);
                card.classList.toggle('border-[#EFF2F5]', !active);
                card.querySelector('i').classList.toggle('text-emerald-600', active);
                card.querySelector('i').classList.toggle('text-[#A1A5B7]', !active);
                card.querySelector('span').classList.toggle('text-emerald-700', active);
                card.querySelector('span').classList.toggle('text-[#5E6278]', !active);
            });
            toggleNomLabel();
        }

        function toggleNomLabel() {
            const particulier = document.getElementById('type_prospect').value === 'Particulier';
            document.getElementById('label-nom-entreprise').innerHTML = particulier
                ? 'Nom complet <span class="text-rose-500">*</span>'
                : "Nom de l'entreprise <span class=\"text-rose-500\">*</span>";
        }
        document.addEventListener('DOMContentLoaded', toggleNomLabel);
    </script>
</x-commercial-layout>
