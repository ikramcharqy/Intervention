<x-super-admin-layout>
    <div class="space-y-6">

        <div>
            <h2 class="text-xl font-bold text-[#131523] dark:text-slate-100">Sécurité de Mon Compte</h2>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Authentification à deux facteurs, sessions actives et historique de connexion de votre compte {{ auth()->user()->roles->pluck('name')->first() ?? 'Super Admin' }}.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Activité du compte -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
                <h3 class="text-[16px] font-bold text-[#131523] dark:text-slate-100 mb-4">Activité du compte</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700">
                        <span class="text-xs font-semibold text-[#5A607F] dark:text-slate-400">Dernière connexion</span>
                        <span class="text-xs font-bold text-[#131523] dark:text-slate-100">
                            {{ $derniereConnexion ? \Illuminate\Support\Carbon::parse($derniereConnexion)->format('d/m/Y à H:i') : 'Première connexion' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700">
                        <span class="text-xs font-semibold text-[#5A607F] dark:text-slate-400">Adresse IP de cette session</span>
                        <span class="text-xs font-bold text-[#131523] dark:text-slate-100 font-mono">{{ $ip }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700">
                        <span class="text-xs font-semibold text-[#5A607F] dark:text-slate-400">Tentatives échouées (24h)</span>
                        <span class="text-xs font-bold {{ $echecsRecents > 0 ? 'text-[#F0142F] dark:text-rose-400' : 'text-[#131523] dark:text-slate-100' }}">
                            {{ $echecsRecents }} tentative{{ $echecsRecents > 1 ? 's' : '' }} échouée{{ $echecsRecents > 1 ? 's' : '' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700">
                        <span class="text-xs font-semibold text-[#5A607F] dark:text-slate-400">Authentification à deux facteurs</span>
                        @if(auth()->user()->two_factor_confirmed_at)
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#E3FBF0] dark:bg-emerald-500/10 text-[#06A561] dark:text-emerald-400">ACTIVÉE</span>
                                <form method="POST" action="{{ route('two-factor.reset') }}" onsubmit="return confirm('Réinitialiser la 2FA ? Un nouvel enrôlement sera nécessaire.');">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-semibold text-[#A1A7C4] dark:text-slate-500 hover:text-[#F0142F] dark:hover:text-rose-400 underline">Réinitialiser</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('two-factor.setup') }}" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#FDE3E6] dark:bg-rose-500/10 text-[#F0142F] dark:text-rose-400 hover:bg-[#F8C4CA] dark:hover:bg-rose-500/20">OBLIGATOIRE — ACTIVER</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mot de passe -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7 flex flex-col justify-between">
                <div>
                    <h3 class="text-[16px] font-bold text-[#131523] dark:text-slate-100 mb-1">Mot de passe</h3>
                    <p class="text-xs text-[#5A607F] dark:text-slate-400">Le changement de mot de passe se fait depuis votre profil général — commun à tous les rôles de la plateforme.</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition">
                    <x-icon name="key" class="w-4 h-4" />
                    <span>Modifier mon mot de passe</span>
                </a>
            </div>
        </div>

        <!-- Sessions actives -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-7 pb-4">
                <div>
                    <h3 class="text-[16px] font-bold text-[#131523] dark:text-slate-100">Sessions actives</h3>
                    <p class="text-xs text-[#5A607F] dark:text-slate-400 mt-1">Appareils actuellement connectés à votre compte.</p>
                </div>
                @if($sessions->count() > 1)
                    <form action="{{ route('superadmin.securite.sessions.revoquerAutres') }}" method="POST" onsubmit="return confirm('Déconnecter tous les autres appareils ?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-[#FDE3E6] dark:bg-rose-500/10 hover:bg-[#F8C4CA] dark:hover:bg-rose-500/20 text-[#F0142F] dark:text-rose-400 text-xs font-bold transition">
                            Déconnecter tous les autres appareils
                        </button>
                    </form>
                @endif
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @foreach($sessions as $session)
                    <div class="flex items-center justify-between gap-4 px-7 py-3.5 flex-wrap">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-[#131523] dark:text-slate-100 truncate">
                                {{ $session->appareil }}
                                @if($session->est_courante)
                                    <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#E3FBF0] dark:bg-emerald-500/10 text-[#06A561] dark:text-emerald-400">Session actuelle</span>
                                @endif
                                @if($session->est_client_automatise)
                                    <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#FFF3DE] dark:bg-amber-500/10 text-[#B98900] dark:text-amber-400">Client API</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 font-mono">{{ $session->ip_address }} · {{ $session->derniere_activite->diffForHumans() }}</p>
                        </div>
                        @unless($session->est_courante)
                            <form action="{{ route('superadmin.securite.sessions.revoquer', $session->id) }}" method="POST" onsubmit="return confirm('Déconnecter cette session ?');">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-[#F0142F] dark:text-rose-400 hover:underline">Déconnecter</button>
                            </form>
                        @endunless
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Historique de connexion -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="p-7 pb-4">
                <h3 class="text-[16px] font-bold text-[#131523] dark:text-slate-100">Historique de connexion</h3>
                <p class="text-xs text-[#5A607F] dark:text-slate-400 mt-1">Les 10 dernières connexions à votre compte.</p>
                {{-- Étape 6 (prompt "Vérification du contournement 2FA") : géolocalisation
                     ville/pays à partir de l'IP — amélioration future documentée, non
                     implémentée dans cette passe (toutes les IP de développement actuelles
                     sont 127.0.0.1, ce qui ne permettrait pas de la tester utilement). --}}
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-7 pr-4">Date</th>
                            <th class="py-3 px-4">Adresse IP</th>
                            <th class="py-3 pr-7 pl-4">Appareil / Navigateur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($historique as $entree)
                            <tr>
                                <td class="py-3 pl-7 pr-4 text-[#131523] dark:text-slate-300">{{ $entree->logged_in_at->format('d/m/Y à H:i') }}</td>
                                <td class="py-3 px-4 text-[#131523] dark:text-slate-300 font-mono">{{ $entree->ip_address ?? '—' }}</td>
                                <td class="py-3 pr-7 pl-4 text-[#131523] dark:text-slate-300">
                                    {{ \Illuminate\Support\Str::limit($entree->user_agent, 60) ?? '—' }}
                                    @if(\App\Services\SessionSecurityService::estClientAutomatise($entree->user_agent))
                                        <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-[#FFF3DE] dark:bg-amber-500/10 text-[#B98900] dark:text-amber-400">Client API</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun historique de connexion pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-super-admin-layout>
