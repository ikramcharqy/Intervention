<x-app-layout>
    <x-slot name="header">
        {{ __('Fiche Client : ') }} {{ $client->nom }}
    </x-slot>

    @php
        $initiales = strtoupper(mb_substr($client->nom, 0, 1));
        $typeIcons = [
            'Entreprise'    => 'ti-building',
            'Particulier'   => 'ti-user',
            'Administration'=> 'ti-building-bank',
        ];
        $activiteIcons = [
            'creation'               => ['ti-plus', '#1E5EFF', '#D9E4FF'],
            'modification'           => ['ti-edit', '#5A607F', '#E6E9F4'],
            'changement_commercial'  => ['ti-user-exclamation', '#F99600', '#FFF4C9'],
            'changement_statut'      => ['ti-toggle-left', '#1FD286', '#C4F8E2'],
            'appel'                  => ['ti-phone', '#1E5EFF', '#D9E4FF'],
            'note'                   => ['ti-note', '#5A607F', '#E6E9F4'],
            'autre'                  => ['ti-dots', '#5A607F', '#E6E9F4'],
        ];
    @endphp

    <div>
        <div class="flex items-center justify-between mb-5">
            <a href="{{ route('clients.index') }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour au portefeuille
            </a>
            <div class="flex gap-2">
                <a href="{{ route('clients.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Fermer</a>
                @can('update', $client)
                    <a href="{{ route('clients.edit', $client) }}" class="ds-btn ds-btn-primary ds-btn-sm">Modifier</a>
                @endcan
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
                <!-- Carte profil du client -->
                <div class="ds-card-elevated p-7">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shrink-0" style="background-color:#D9E4FF;">
                                <span class="text-xl font-bold" style="color:#1E5EFF;">{{ $initiales }}</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-[#131523]">{{ $client->nom }}</h2>
                                <p class="text-xs text-[#5A607F] mt-0.5 inline-flex items-center gap-1">
                                    <i class="ti {{ $typeIcons[$client->type_client] ?? 'ti-user' }}"></i> {{ $client->type_client }} &middot; {{ $client->ville }}
                                </p>
                                <p class="text-xs text-[#A1A7C4] mt-1">
                                    {{ $client->code_client }} &middot; {{ $client->chantiers->count() }} chantier(s)
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            <span class="ds-badge ds-badge-sm {{ $client->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                                {{ $client->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            @if(!$client->commercial_id)
                                <span class="ds-badge ds-badge-sm ds-badge-light-warning">Non assigné</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t mt-6 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm" style="border-color:#E6E9F4;">
                        <div><span class="text-[#A1A7C4]">Nom du contact :</span> <span class="text-[#131523] font-medium">{{ $client->nom_contact ?? '-' }}</span></div>
                        <div><span class="text-[#A1A7C4]">Téléphone :</span> <span class="text-[#131523] font-medium">{{ $client->telephone }}</span></div>
                        <div><span class="text-[#A1A7C4]">Téléphone secondaire :</span> <span class="text-[#131523] font-medium">{{ $client->telephone_secondaire ?? '-' }}</span></div>
                        <div><span class="text-[#A1A7C4]">E-mail :</span> <span class="text-[#131523] font-medium">{{ $client->email ?? '-' }}</span></div>
                        <div class="md:col-span-2"><span class="text-[#A1A7C4]">Adresse de facturation :</span> <span class="text-[#131523] font-medium">{{ $client->adresse_facturation ?? '-' }}</span></div>
                    </div>

                    @if ($client->type_client === 'Entreprise' && $client->clientEntreprise)
                        <div class="border-t mt-5 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm" style="border-color:#E6E9F4;">
                            <div><span class="text-[#A1A7C4]">ICE :</span> <span class="text-[#131523] font-medium">{{ $client->clientEntreprise->ice ?? '-' }}</span></div>
                            <div><span class="text-[#A1A7C4]">Identifiant Fiscal (IF) :</span> <span class="text-[#131523] font-medium">{{ $client->clientEntreprise->if ?? '-' }}</span></div>
                            <div><span class="text-[#A1A7C4]">Registre du Commerce (RC) :</span> <span class="text-[#131523] font-medium">{{ $client->clientEntreprise->rc ?? '-' }}</span></div>
                            <div><span class="text-[#A1A7C4]">Patente :</span> <span class="text-[#131523] font-medium">{{ $client->clientEntreprise->patente ?? '-' }}</span></div>
                        </div>
                    @endif

                    @if ($client->type_client === 'Particulier' && $client->clientParticulier && auth()->user()->can('viewIdentity', $client))
                        <div class="border-t mt-5 pt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm" style="border-color:#E6E9F4;">
                            <div><span class="text-[#A1A7C4]">Numéro CIN :</span> <span class="text-[#131523] font-medium">{{ $client->clientParticulier->numero_cin ?? '-' }}</span></div>
                            <div><span class="text-[#A1A7C4]">Date de naissance :</span> <span class="text-[#131523] font-medium">{{ $client->clientParticulier->date_naissance?->format('d/m/Y') ?? '-' }}</span></div>
                        </div>
                    @endif

                    <div class="border-t mt-6 pt-5" style="border-color:#E6E9F4;">
                        <h4 class="text-sm font-bold text-[#131523] mb-1">Observations</h4>
                        <div class="rounded-lg border p-3 text-sm text-[#5A607F] min-h-[50px]" style="border-color:#D9E1EC; background-color:#F5F6FA;">
                            {{ $client->observations ?: 'Aucune observation renseignée.' }}
                        </div>
                    </div>
                </div>

                <!-- Historique / Activité -->
                <div class="ds-card-elevated overflow-hidden">
                    <div class="p-7 pb-0">
                        <h3 class="text-[16px] font-bold text-[#131523]">Historique / Activité</h3>
                        <p class="text-xs text-[#A1A7C4] mt-0.5">Timeline chronologique des actions effectuées sur ce client.</p>
                    </div>
                    <div class="p-7">
                        @forelse ($client->activites as $activite)
                            @php [$icon, $color, $bg] = $activiteIcons[$activite->type_action] ?? $activiteIcons['autre']; @endphp
                            <div class="flex gap-3 {{ !$loop->last ? 'pb-5' : '' }}">
                                <div class="flex flex-col items-center shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background-color:{{ $bg }};">
                                        <i class="ti {{ $icon }} text-sm" style="color:{{ $color }};"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="w-px flex-1 mt-1" style="background-color:#E6E9F4;"></div>
                                    @endif
                                </div>
                                <div class="pb-1 flex-1">
                                    <p class="text-sm font-medium text-[#131523]">{{ $activite->description }}</p>
                                    <p class="text-[11px] text-[#A1A7C4] mt-0.5">
                                        {{ $activite->user->name ?? 'Système' }} &middot; {{ $activite->created_at->diffForHumans() }}
                                        &middot; {{ $activite->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-[#A1A7C4] text-center py-4">Aucune activité enregistrée pour ce client.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Chantiers -->
                <div class="ds-card-elevated overflow-hidden">
                    <div class="p-7 pb-0 flex items-center justify-between">
                        <h3 class="text-[16px] font-bold text-[#131523]">Chantiers ({{ $client->chantiers->count() }})</h3>
                        <a href="{{ route('chantiers.create', ['client_id' => $client->id]) }}" class="ds-btn ds-btn-primary ds-btn-sm">
                            <i class="ti ti-plus"></i> Ajouter un chantier
                        </a>
                    </div>
                    <div class="overflow-x-auto mt-5">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Type Local</th>
                                    <th>Ville</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($client->chantiers as $chantier)
                                    <tr>
                                        <td class="font-mono text-xs text-[#5A607F]">{{ $chantier->code_chantier }}</td>
                                        <td class="font-semibold text-[#131523]">{{ $chantier->nom }}</td>
                                        <td class="text-[#5A607F]">{{ $chantier->type_local }}</td>
                                        <td class="text-[#5A607F]">{{ $chantier->ville }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('chantiers.show', $chantier) }}" class="text-xs font-semibold" style="color:#1E5EFF;">Consulter</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-xs text-[#A1A7C4]">Aucun chantier enregistré pour ce client.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Contacts -->
                <div class="ds-card-elevated overflow-hidden" x-data="{ showContactForm: false }">
                    <div class="p-7 pb-0 flex items-center justify-between">
                        <h3 class="text-[16px] font-bold text-[#131523]">Contacts ({{ $client->contacts->count() }})</h3>
                        <button @click="showContactForm = !showContactForm" class="ds-btn ds-btn-primary ds-btn-sm">
                            <i class="ti ti-plus"></i> Ajouter un contact
                        </button>
                    </div>

                    <div x-show="showContactForm" x-collapse class="mx-7 mt-5 p-4 rounded-lg" style="background-color:#F5F6FA;">
                        <form action="{{ route('clients.contacts.store', $client) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="ds-label">Nom *</label>
                                    <input type="text" name="nom" required class="ds-input" placeholder="Nom">
                                </div>
                                <div>
                                    <label class="ds-label">Prénom</label>
                                    <input type="text" name="prenom" class="ds-input" placeholder="Prénom">
                                </div>
                                <div>
                                    <label class="ds-label">Fonction</label>
                                    <input type="text" name="fonction" class="ds-input" placeholder="Ex: Directeur technique">
                                </div>
                                <div>
                                    <label class="ds-label">Email</label>
                                    <input type="email" name="email" class="ds-input" placeholder="email@example.com">
                                </div>
                                <div>
                                    <label class="ds-label">Téléphone</label>
                                    <input type="text" name="telephone" class="ds-input" placeholder="+212...">
                                </div>
                                <div class="flex items-center gap-2 mt-5">
                                    <input type="checkbox" name="is_principal" value="1" id="is_principal" class="rounded">
                                    <label for="is_principal" class="text-xs font-medium text-[#131523]">Contact principal</label>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end gap-2">
                                <button type="button" @click="showContactForm = false" class="ds-btn ds-btn-white ds-btn-sm">Annuler</button>
                                <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Enregistrer</button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto mt-5">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Fonction</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Principal</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($client->contacts as $contact)
                                    <tr>
                                        <td class="font-semibold text-[#131523]">{{ $contact->prenom }} {{ $contact->nom }}</td>
                                        <td class="text-[#5A607F]">{{ $contact->fonction ?? '-' }}</td>
                                        <td class="text-[#5A607F]">{{ $contact->email ?? '-' }}</td>
                                        <td class="text-[#5A607F]">{{ $contact->telephone ?? '-' }}</td>
                                        <td>
                                            @if($contact->is_principal)
                                                <span class="ds-badge ds-badge-sm ds-badge-light-success">Principal</span>
                                            @else
                                                <span class="text-[#A1A7C4] text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contact ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-xs text-[#A1A7C4]">Aucun contact enregistré pour ce client.</td>
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
                    <h3 class="text-[16px] font-bold text-[#131523] mb-4">Aperçu</h3>

                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Commercial assigné</dt>
                            <dd class="mt-0.5">
                                @if($client->commercial)
                                    <span class="text-[#131523] font-medium">{{ $client->commercial->prenom }} {{ $client->commercial->name }}</span>
                                @else
                                    <span class="text-[#F99600] font-medium">Non assigné</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Statut</dt>
                            <dd class="mt-0.5">
                                <span class="ds-badge ds-badge-sm {{ $client->is_active ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
                                    {{ $client->is_active ? 'Compte Actif' : 'Compte Désactivé' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] uppercase tracking-wide font-semibold" style="color:#A1A7C4;">Client depuis</dt>
                            <dd class="text-[#5A607F] mt-0.5">{{ $client->created_at->format('d/m/Y') }}</dd>
                        </div>
                    </dl>

                    @can('reassign', $client)
                        <div class="border-t mt-5 pt-5" style="border-color:#E6E9F4;">
                            <h4 class="text-sm font-bold text-[#131523] mb-3">Réassigner à un commercial</h4>
                            <form method="POST" action="{{ route('clients.reassign', $client) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <select name="nouveau_commercial_id" required class="ds-input">
                                        <option value="">Choisir un commercial...</option>
                                        @foreach($commerciaux as $com)
                                            <option value="{{ $com->id }}" @selected($com->id === $client->commercial_id)>
                                                {{ $com->prenom }} {{ $com->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nouveau_commercial_id') <p class="text-xs mt-1" style="color:#F0142F;">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <input type="text" name="commentaire" placeholder="Commentaire (optionnel)" class="ds-input">
                                </div>
                                <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm w-full">Réassigner</button>
                            </form>
                        </div>
                    @endcan

                    <div class="border-t mt-5 pt-4 flex flex-col gap-2" style="border-color:#E6E9F4;">
                        @can('update', $client)
                            @if ($client->is_active)
                                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Désactiver ce client ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Désactiver le compte</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('clients.restore', $client) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold" style="color:#06A561;">Réactiver le compte</button>
                                </form>
                            @endif
                        @endcan

                        @can('forceDelete', $client)
                            <form method="POST" action="{{ route('clients.forceDelete', $client) }}" onsubmit="return confirm('ATTENTION : Supprimer DEFINITIVEMENT ce client et toutes ses données associées (chantiers, interventions...) ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold" style="color:#F0142F;">Supprimer définitivement</button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="ds-card-elevated p-7">
                    <h3 class="text-[16px] font-bold text-[#131523] mb-1">Étiquettes</h3>
                    <p class="text-[11px] text-[#A1A7C4] mb-4">Type, ville et statut d'assignation.</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                            {{ $client->type_client }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#F1F4FA; color:#5A607F;">
                            {{ $client->ville }}
                        </span>
                        @if(!$client->commercial_id)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color:#FFF4C9; color:#F99600;">
                                Non assigné
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
