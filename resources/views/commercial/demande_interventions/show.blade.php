<x-commercial-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Demande : {{ $demandeIntervention->reference }}
            </h2>
            @if($demandeIntervention->statut === 'En attente')
                <a href="{{ route('demande-interventions.edit', $demandeIntervention) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm font-medium">
                    Modifier
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Statut Badge --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Statut de la demande</p>
                        @php
                            $statutColors = [
                                'En attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'Acceptée' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'Refusée' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                'Planifiée' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            ];
                            $color = $statutColors[$demandeIntervention->statut] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1.5 text-sm font-bold rounded-full {{ $color }}">
                            {{ $demandeIntervention->statut }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Soumise le</p>
                        <p class="text-sm font-semibold">{{ $demandeIntervention->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Informations principales --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Informations de la demande</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Référence</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Commercial</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->commercial->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Client</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-semibold">{{ $demandeIntervention->client->nom ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Chantier</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->chantier->nom ?? 'Non spécifié' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Type d'intervention</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->typeIntervention->nom ?? 'Non spécifié' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Priorité</dt>
                        <dd>
                            @php
                                $prioColors = ['Urgente' => 'text-red-600 font-bold', 'Haute' => 'text-orange-600 font-semibold', 'Normale' => 'text-gray-900', 'Faible' => 'text-gray-500'];
                            @endphp
                            <span class="{{ $prioColors[$demandeIntervention->priorite] ?? 'text-gray-900' }}">
                                {{ $demandeIntervention->priorite }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">Objet</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-semibold text-base">{{ $demandeIntervention->objet }}</dd>
                    </div>
                    @if($demandeIntervention->devis->isNotEmpty())
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 font-medium">Devis d'origine</dt>
                            <dd>
                                <a href="{{ route('commercial.devis.show', $demandeIntervention->devis->first()) }}" class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                                    <i class="fas fa-file-invoice-dollar text-xs"></i>
                                    Voir le devis {{ $demandeIntervention->devis->first()->reference }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($demandeIntervention->description)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">Description</dt>
                        <dd class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $demandeIntervention->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Photos --}}
            @if(!empty($demandeIntervention->photos))
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Photos ({{ count($demandeIntervention->photos) }})</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($demandeIntervention->photos as $photo)
                        <a href="{{ Storage::url($photo) }}" target="_blank">
                            <img src="{{ Storage::url($photo) }}" alt="Photo" class="w-full h-32 object-cover rounded-lg hover:opacity-80 transition">
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Documents --}}
            @if(!empty($demandeIntervention->documents))
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Documents ({{ count($demandeIntervention->documents) }})</h3>
                <ul class="space-y-2">
                    @foreach($demandeIntervention->documents as $doc)
                        <li>
                            <a href="{{ Storage::url($doc) }}" target="_blank"
                               class="flex items-center gap-2 text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ basename($doc) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Qualification Commerciale (Appel / Email / Validation / Refus) --}}
            @if($demandeIntervention->statut === 'En attente')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-2 border-indigo-500/40">
                    <h3 class="text-base font-extrabold text-indigo-700 dark:text-indigo-400 border-b border-gray-100 dark:border-gray-700 pb-3 mb-4 flex items-center space-x-2">
                        <i class="fa-solid fa-phone-volume"></i>
                        <span>Qualification Commerciale (Échange Client par Téléphone / Email)</span>
                    </h3>

                    <p class="text-xs text-gray-600 dark:text-gray-300 mb-4">
                        Consultez la demande, puis contactez le client par téléphone (<strong>{{ $demandeIntervention->client->telephone ?? 'Non renseigné' }}</strong>) ou email (<strong>{{ $demandeIntervention->client->email ?? 'Non renseigné' }}</strong>). Complétez le compte-rendu pour <strong>Valider</strong> (transmet l'intervention à l'Admin) ou <strong>Refuser</strong> la demande.
                    </p>

                    <form action="{{ route('demande-interventions.validerConvertir', $demandeIntervention) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Compte-rendu d'échange (Téléphone / Email) <span class="text-red-500">*</span></label>
                                <textarea name="compte_rendu_echange" rows="3" required placeholder="Ex: Client contacté par téléphone. Demande confirmée et détails d'accès validés..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 shadow-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Date d'intervention souhaitée par le client</label>
                                <input type="datetime-local" name="date_prevue_souhaitee" class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Notes internes Commercial</label>
                            <input type="text" name="notes_commercial" placeholder="Notes visibles par l'administration..." class="w-full text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-indigo-500 shadow-sm">
                        </div>

                        <div class="flex flex-wrap gap-3 pt-2">
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center space-x-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Valider & Transmettre à l'Admin pour Planification</span>
                            </button>
                        </div>
                    </form>

                    <hr class="my-4 border-gray-200 dark:border-gray-700">

                    {{-- Formulaire de Refus --}}
                    <form action="{{ route('demande-interventions.refuser', $demandeIntervention) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cette demande ?');" class="flex items-center gap-3">
                        @csrf
                        <input type="text" name="motif_refus" required placeholder="Motif du refus à notifier au client..." class="flex-1 text-xs rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-red-500 shadow-sm">
                        <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition shrink-0 flex items-center space-x-1">
                            <i class="fa-solid fa-ban"></i>
                            <span>Refuser la Demande</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 text-xs font-bold text-gray-700 dark:text-gray-300">
                    Statut actuel de la demande : <span class="uppercase text-indigo-600">{{ $demandeIntervention->statut }}</span>
                </div>
            @endif

            <div class="text-right">
                <a href="{{ route('demande-interventions.index') }}" class="text-sm text-gray-500 hover:underline">
                    ← Retour à la liste des demandes
                </a>
            </div>
        </div>
    </div>
</x-commercial-layout>
