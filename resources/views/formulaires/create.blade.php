<x-super-admin-layout>
    <div class="space-y-6">
        <!-- Titre + Actions -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('formulaires.index') }}" class="w-9 h-9 rounded-[4px] border border-[#E6E9F4] flex items-center justify-center text-[#5A607F] hover:bg-[#F5F6FA] transition shrink-0">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Nouveau Protocole d'Intervention</h1>
                    <p class="text-xs text-[#5A607F] mt-1">Le formulaire que le technicien remplira sur le terrain pour ce type d'intervention</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('formulaires.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Annuler</a>
                <button type="submit" form="form-nouveau-formulaire" class="ds-btn ds-btn-primary ds-btn-sm">
                    Continuer vers les questions <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- Stepper -->
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-[#1E5EFF] text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                <span class="text-xs font-bold text-[#131523]">Informations générales</span>
            </div>
            <div class="flex-1 h-px bg-[#E6E9F4]"></div>
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-[#F5F6FA] border border-[#E6E9F4] text-[#A1A7C4] text-xs font-bold flex items-center justify-center shrink-0">2</span>
                <span class="text-xs font-bold text-[#A1A7C4]">Questions &amp; choix</span>
            </div>
        </div>

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

        <form id="form-nouveau-formulaire" method="POST" action="{{ route('formulaires.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <!-- Colonne principale : Informations -->
                <div class="lg:col-span-2 ds-card-elevated overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#E6E9F4]">
                        <h3 class="text-sm font-bold text-[#131523]">Informations</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="nom" class="ds-label">Nom du protocole *</label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                   placeholder="Ex : Rapport Installation Fibre Optique"
                                   class="ds-input">
                        </div>

                        <div>
                            <label for="type_intervention_id" class="ds-label">Type d'intervention *</label>
                            <select id="type_intervention_id" name="type_intervention_id" required class="ds-input">
                                <option value="">-- Sélectionner --</option>
                                @foreach($typesIntervention as $type)
                                    <option value="{{ $type->id }}" {{ old('type_intervention_id', $preselectedTypeId ?? null) == $type->id ? 'selected' : '' }}>
                                        {{ $type->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-[#A1A7C4] mt-1.5">Seuls les types sans protocole existant sont affichés.</p>
                        </div>

                        <div>
                            <label for="description" class="ds-label">Description</label>
                            <textarea id="description" name="description" rows="5"
                                      placeholder="Décrivez l'objectif de ce protocole (ce qui sera vérifié, testé, documenté sur le terrain)..."
                                      class="ds-input !h-auto py-3 resize-none">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Colonne latérale : Prochaine étape -->
                <div class="ds-card-elevated overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#E6E9F4]">
                        <h3 class="text-sm font-bold text-[#131523]">Étape suivante</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="p-3.5 rounded-[6px] bg-[#EAF0FF] border border-[#D9E4FF] flex items-start gap-2.5">
                            <i class="fas fa-list-check text-[#1E5EFF] text-sm shrink-0 mt-0.5"></i>
                            <p class="text-[11px] text-[#5A607F] leading-relaxed">Après validation de ces informations, vous serez amené à construire le contenu réel du protocole : questions, types de champs, choix, options — c'est ce que le technicien verra et remplira sur le terrain.</p>
                        </div>
                        <div class="p-3.5 rounded-[6px] bg-[#FFF3DE] border border-[#FFE7B8] flex items-start gap-2.5">
                            <i class="fas fa-triangle-exclamation text-[#B98900] text-sm shrink-0 mt-0.5"></i>
                            <p class="text-[11px] text-[#5A607F] leading-relaxed">Le protocole reste <strong class="text-[#131523]">inactif</strong> tant qu'aucune question n'a été ajoutée — il ne peut pas être envoyé aux techniciens à l'état vide.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-super-admin-layout>
