<x-client-layout>
    <x-slot name="header">Mon Profil</x-slot>

    <div class="space-y-6">

        <!-- En-tête résumé -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-16 h-16 rounded-full bg-emerald-500 text-white font-extrabold text-xl flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($user->prenom ?? $user->name, 0, 1) . substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-lg font-extrabold text-[#1e2530] truncate">{{ $user->prenom }} {{ $user->name }}</h1>
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Actif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Client · Membre depuis {{ $user->created_at->translatedFormat('F Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Bandeau d'informations -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-3 mt-6 pt-6 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <i class="fas fa-envelope w-4 text-center text-slate-300 text-xs"></i>
                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Email</span>
                    <span class="text-xs font-semibold text-slate-700 truncate">{{ $user->email }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-hashtag w-4 text-center text-slate-300 text-xs"></i>
                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Code client</span>
                    <span class="text-xs font-semibold text-slate-700 font-mono">{{ $client->code_client ?? '—' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-phone w-4 text-center text-slate-300 text-xs"></i>
                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Téléphone</span>
                    <span class="text-xs font-semibold text-slate-700">{{ $user->telephone ?? '—' }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-shield-halved w-4 text-center text-slate-300 text-xs"></i>
                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Mot de passe</span>
                    <a href="{{ route('client.securite.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Gérer dans Sécurité →</a>
                </div>
            </div>
        </div>

        <!-- Organisation -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Organisation</h2>
            <p class="text-xs text-slate-400 mb-4">Société rattachée à votre compte — pour toute correction, contactez votre commercial via le Support.</p>

            @if($client)
                <div class="flex items-center justify-between gap-4 p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex-wrap">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $client->nom }}</p>
                        <p class="text-[11px] text-slate-400">{{ $client->type_client }}</p>
                    </div>
                    <span class="text-[11px] text-slate-400 shrink-0">Depuis {{ $client->created_at->translatedFormat('d M Y') }}</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    @if($client->clientEntreprise?->ice)
                        <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">ICE</p><p class="text-xs font-semibold text-slate-900 mt-1 font-mono">{{ $client->clientEntreprise->ice }}</p></div>
                    @endif
                    @if($client->clientEntreprise?->if)
                        <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">IF</p><p class="text-xs font-semibold text-slate-900 mt-1 font-mono">{{ $client->clientEntreprise->if }}</p></div>
                    @endif
                    @if($client->clientEntreprise?->rc)
                        <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">RC</p><p class="text-xs font-semibold text-slate-900 mt-1 font-mono">{{ $client->clientEntreprise->rc }}</p></div>
                    @endif
                    @if($client->clientEntreprise?->patente)
                        <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Patente</p><p class="text-xs font-semibold text-slate-900 mt-1 font-mono">{{ $client->clientEntreprise->patente }}</p></div>
                    @endif
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Adresse de facturation</p>
                        <p class="text-xs font-semibold text-slate-900 mt-1">{{ $client->adresse_facturation ?? '—' }}{{ $client->ville ? ', '.$client->ville : '' }}</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-400 italic">Aucune fiche société rattachée à votre compte.</p>
            @endif
        </div>

        <!-- Informations Personnelles (formulaire) -->
        <div class="bg-white rounded-2xl shadow-sm p-6 max-w-2xl">
            <h2 class="text-sm font-extrabold text-slate-900 mb-1">Informations Personnelles</h2>
            <p class="text-xs text-slate-400 mb-5">Vos coordonnées de contact sur le portail.</p>

            <form action="{{ route('client.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nom</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                        @error('name') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required
                               class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                        @error('prenom') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Adresse Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                    @error('email') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}"
                           class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                    @error('telephone') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Adresse</label>
                    <textarea name="adresse" rows="3"
                              class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm resize-none">{{ old('adresse', $user->adresse) }}</textarea>
                    @error('adresse') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition">
                        <i class="fas fa-check"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-client-layout>
