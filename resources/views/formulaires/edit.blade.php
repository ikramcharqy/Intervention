<x-super-admin-layout>
    <div class="space-y-6">
        <!-- Titre + Actions -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('formulaires.show', $formulaire) }}" class="w-9 h-9 rounded-[4px] border border-[#E6E9F4] flex items-center justify-center text-[#5A607F] hover:bg-[#F5F6FA] transition shrink-0">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Modifier : {{ $formulaire->nom }}</h1>
                    <p class="text-xs text-[#5A607F] mt-1">Informations générales du protocole</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('formulaires.show', $formulaire) }}" class="ds-btn ds-btn-white ds-btn-sm">Annuler</a>
                <button type="submit" form="form-edit-formulaire" class="ds-btn ds-btn-primary ds-btn-sm">
                    <i class="fas fa-check"></i> Mettre à jour
                </button>
            </div>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0 mt-0.5"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-edit-formulaire" method="POST" action="{{ route('formulaires.update', $formulaire) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Colonne principale : Informations -->
                <div class="lg:col-span-2 ds-card-elevated overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#E6E9F4]">
                        <h3 class="text-sm font-bold text-[#131523]">Informations</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="nom" class="ds-label">Nom du protocole *</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom', $formulaire->nom) }}" required class="ds-input">
                        </div>

                        <div>
                            <label for="type_intervention_id" class="ds-label">Type d'intervention *</label>
                            <select id="type_intervention_id" name="type_intervention_id" required class="ds-input">
                                @foreach($typesIntervention as $type)
                                    <option value="{{ $type->id }}" {{ old('type_intervention_id', $formulaire->type_intervention_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="description" class="ds-label">Description</label>
                            <textarea id="description" name="description" rows="5"
                                      class="ds-input !h-auto py-3 resize-none">{{ old('description', $formulaire->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale : Statut -->
                <div class="ds-card-elevated overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#E6E9F4]">
                        <h3 class="text-sm font-bold text-[#131523]">Statut</h3>
                    </div>
                    <div class="p-6">
                        @php $peutActiver = $formulaire->questions()->count() > 0; @endphp
                        <label for="is_active" class="flex items-center justify-between {{ $peutActiver ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed' }}">
                            <span>
                                <span class="block text-xs font-bold text-[#131523]">Protocole actif</span>
                                <span class="block text-[11px] text-[#A1A7C4] mt-0.5">Visible et utilisable par les techniciens</span>
                            </span>
                            <span class="relative inline-flex items-center shrink-0">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $formulaire->is_active) ? 'checked' : '' }}
                                       {{ $peutActiver ? '' : 'disabled' }}
                                       class="peer sr-only">
                                <span class="w-10 h-6 rounded-full bg-[#E6E9F4] peer-checked:bg-[#1E5EFF] transition-colors"></span>
                                <span class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white transition-transform peer-checked:translate-x-4"></span>
                            </span>
                        </label>

                        @unless($peutActiver)
                            <p class="text-[11px] text-[#B98900] mt-3">
                                <i class="fas fa-triangle-exclamation mr-1"></i>
                                Ajoutez au moins une question pour pouvoir activer ce protocole.
                            </p>
                        @endunless
                    </div>

                    <div class="px-6 pb-6">
                        <a href="{{ route('formulaires.show', $formulaire) }}" class="ds-btn ds-btn-white ds-btn-sm w-full justify-center">
                            <i class="fas fa-list-check"></i> Gérer les questions
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-super-admin-layout>
