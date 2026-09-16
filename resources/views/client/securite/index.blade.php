<x-client-layout>
    <x-slot name="header">Sécurité</x-slot>

    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Activité du compte -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-slate-900 mb-4">Activité du compte</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-xs font-semibold text-slate-500">Dernière connexion</span>
                        <span class="text-xs font-bold text-slate-900">
                            {{ $derniereConnexion ? \Illuminate\Support\Carbon::parse($derniereConnexion)->format('d/m/Y à H:i') : 'Première connexion' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-xs font-semibold text-slate-500">Adresse IP de cette session</span>
                        <span class="text-xs font-bold text-slate-900 font-mono">{{ $ip }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-xs font-semibold text-slate-500">Authentification à deux facteurs</span>
                        <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold rounded-full border bg-indigo-50 text-indigo-600 border-indigo-200">
                            Bientôt disponible
                        </span>
                    </div>
                </div>
            </div>

            <!-- Changer le mot de passe -->
            <div class="bg-white rounded-2xl shadow-sm p-6" x-data="passwordForm()">
                <h2 class="text-sm font-extrabold text-slate-900 mb-1">Mot de passe</h2>
                <p class="text-xs text-slate-400 mb-5">Choisissez un mot de passe robuste, différent de vos autres comptes.</p>

                <form action="{{ route('client.profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mot de passe actuel</label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 pr-10 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('current_password') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nouveau mot de passe</label>
                        <div class="relative">
                            <input :type="showNew ? 'text' : 'password'" name="password" x-model="password" required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 pr-10 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                            <button type="button" @click="showNew = !showNew" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-rose-500 text-[11px]">{{ $message }}</span> @enderror

                        <!-- Indicateur de robustesse en direct -->
                        <div class="mt-2 space-y-1">
                            <div class="flex gap-1">
                                <template x-for="i in 4">
                                    <div class="h-1 flex-1 rounded-full" :class="score >= i ? barColor : 'bg-slate-100'"></div>
                                </template>
                            </div>
                            <ul class="text-[10px] text-slate-400 grid grid-cols-2 gap-x-3 gap-y-0.5 mt-1.5">
                                <li :class="password.length >= 8 ? 'text-emerald-600 font-semibold' : ''"><i class="fas" :class="password.length >= 8 ? 'fa-check' : 'fa-circle text-[4px] align-middle'"></i> 8 caractères min.</li>
                                <li :class="/[0-9]/.test(password) ? 'text-emerald-600 font-semibold' : ''"><i class="fas" :class="/[0-9]/.test(password) ? 'fa-check' : 'fa-circle text-[4px] align-middle'"></i> Un chiffre</li>
                                <li :class="/[A-Z]/.test(password) ? 'text-emerald-600 font-semibold' : ''"><i class="fas" :class="/[A-Z]/.test(password) ? 'fa-check' : 'fa-circle text-[4px] align-middle'"></i> Une majuscule</li>
                                <li :class="/[^A-Za-z0-9]/.test(password) ? 'text-emerald-600 font-semibold' : ''"><i class="fas" :class="/[^A-Za-z0-9]/.test(password) ? 'fa-check' : 'fa-circle text-[4px] align-middle'"></i> Un caractère spécial</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Confirmer le nouveau mot de passe</label>
                        <div class="relative">
                            <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 pr-10 text-xs text-slate-900 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 shadow-sm">
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400">Par sécurité, vos autres sessions seront automatiquement déconnectées après ce changement.</p>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl transition">
                            <i class="fas fa-key"></i> Modifier le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sessions actives -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-6 pb-4">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Sessions actives</h2>
                    <p class="text-xs text-slate-400 mt-1">Appareils actuellement connectés à votre compte.</p>
                </div>
                @if($sessions->count() > 1)
                    <form action="{{ route('client.securite.sessions.revoquerAutres') }}" method="POST" onsubmit="return confirm('Déconnecter tous les autres appareils ?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition">
                            <i class="fas fa-power-off"></i> Déconnecter tous les autres appareils
                        </button>
                    </form>
                @endif
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($sessions as $session)
                    <div class="flex items-center justify-between gap-4 px-6 py-3.5 flex-wrap">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <i class="fas fa-desktop text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 truncate">
                                    {{ $session->appareil }}
                                    @if($session->est_courante)
                                        <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Session actuelle</span>
                                    @endif
                                </p>
                                <p class="text-[11px] text-slate-400 font-mono">{{ $session->ip_address }} · {{ $session->derniere_activite->diffForHumans() }}</p>
                            </div>
                        </div>
                        @unless($session->est_courante)
                            <form action="{{ route('client.securite.sessions.revoquer', $session->id) }}" method="POST" onsubmit="return confirm('Déconnecter cette session ?');">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700">Déconnecter</button>
                            </form>
                        @endunless
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Historique de connexion -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 pb-4">
                <h2 class="text-sm font-extrabold text-slate-900">Historique de connexion</h2>
                <p class="text-xs text-slate-400 mt-1">Les 10 dernières connexions à votre compte.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Date</th>
                            <th class="py-3 px-4">Adresse IP</th>
                            <th class="py-3 pr-6 pl-4">Appareil / Navigateur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($historique as $entree)
                            <tr>
                                <td class="py-3 pl-6 pr-4 text-slate-600">{{ $entree->logged_in_at->format('d/m/Y à H:i') }}</td>
                                <td class="py-3 px-4 text-slate-600 font-mono">{{ $entree->ip_address ?? '—' }}</td>
                                <td class="py-3 pr-6 pl-4 text-slate-600">{{ \Illuminate\Support\Str::limit($entree->user_agent, 60) ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-slate-400 italic">Aucun historique de connexion pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function passwordForm() {
            return {
                password: '',
                showCurrent: false,
                showNew: false,
                showConfirm: false,
                get score() {
                    let s = 0;
                    if (this.password.length >= 8) s++;
                    if (/[0-9]/.test(this.password)) s++;
                    if (/[A-Z]/.test(this.password)) s++;
                    if (/[^A-Za-z0-9]/.test(this.password)) s++;
                    return s;
                },
                get barColor() {
                    return ['bg-rose-400', 'bg-rose-400', 'bg-amber-400', 'bg-amber-400', 'bg-emerald-500'][this.score] ?? 'bg-slate-100';
                },
            };
        }
    </script>
</x-client-layout>
