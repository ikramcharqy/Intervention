<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#181C32] font-heading flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    Arbitrage — Refus Techniciens &amp; Réaffectation
                </h1>
                <p class="text-sm text-[#A1A5B7] mt-0.5">Gestion des demandes de réaffectation suite aux refus motivés transmis par les techniciens</p>
            </div>
            <span class="px-4 py-2 bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold rounded-xl flex items-center gap-2">
                <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                {{ $demandes->total() }} demande(s) enregistrée(s)
            </span>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="metronic-card overflow-hidden">
            <div class="px-6 py-4 border-b border-[#EFF2F5] bg-gradient-to-r from-rose-50/60 to-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-sm font-bold text-[#181C32]">Demandes de Réaffectation en attente d'Arbitrage Admin</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-[#F9F9FB] border-b border-[#EFF2F5] text-[10px] font-bold uppercase tracking-wider text-[#A1A5B7]">
                            <th class="px-6 py-4">Intervention</th>
                            <th class="px-6 py-4">Technicien Réclamant</th>
                            <th class="px-6 py-4">Motif du Refus</th>
                            <th class="px-6 py-4">Statut Demande</th>
                            <th class="px-6 py-4">Émise Le</th>
                            <th class="px-6 py-4 text-right">Décision &amp; Arbitrage Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F8FA]">
                        @forelse($demandes as $demande)
                            <tr class="hover:bg-[#F9F9FB] transition">
                                <td class="px-6 py-4 font-mono font-bold text-[#3E97FF] text-sm">
                                    <a href="{{ route('interventions.show', $demande->intervention_id) }}" class="hover:underline flex items-center gap-2">
                                        #{{ $demande->intervention->code_intervention ?? 'INT-'.$demande->intervention_id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-[#181C32] text-xs">
                                        👨‍🔧 {{ $demande->technicien->name ?? 'N/A' }} {{ $demande->technicien->prenom ?? '' }}
                                    </div>
                                    <div class="text-[11px] text-[#A1A5B7]">{{ $demande->technicien->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="p-3 bg-amber-50/80 border border-amber-200 text-amber-900 rounded-xl text-xs max-w-sm" title="{{ $demande->motif }}">
                                        <strong>Motif :</strong> "{{ $demande->motif }}"
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($demande->statut === 'En attente')
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 border border-amber-300 rounded-full font-extrabold text-[10px] uppercase inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            En attente
                                        </span>
                                    @elseif($demande->statut === 'Acceptee')
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-full font-extrabold text-[10px] uppercase">
                                            Acceptée par Admin
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-rose-100 text-rose-800 border border-rose-300 rounded-full font-extrabold text-[10px] uppercase">
                                            Refusée par Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[#A1A5B7] text-xs whitespace-nowrap">
                                    {{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($demande->statut === 'En attente')
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Action Accepter & Réaffecter -->
                                            <form action="{{ route('demandes-reaffectation.traiter', $demande->id) }}" method="POST" class="flex items-center gap-1.5">
                                                @csrf
                                                <input type="hidden" name="action" value="accepter">
                                                <select name="nouveau_technicien_id" class="text-xs py-1.5 px-2.5 border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-emerald-500">
                                                    <option value="">-- Remettre en attente --</option>
                                                    @foreach($techniciens as $t)
                                                        @if($t->id !== $demande->technicien_id)
                                                            <option value="{{ $t->id }}">{{ $t->name }} {{ $t->prenom }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition text-xs shadow-sm flex items-center gap-1">
                                                    ✓ Accepter
                                                </button>
                                            </form>

                                            <!-- Action Refuser -->
                                            <form action="{{ route('demandes-reaffectation.traiter', $demande->id) }}" method="POST" onsubmit="return confirm('Confirmez-vous le refus de la demande ? Le technicien initial sera contraint d\'exécuter l\'intervention.');">
                                                @csrf
                                                <input type="hidden" name="action" value="refuser">
                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition text-xs shadow-sm flex items-center gap-1">
                                                    ✕ Forcer Technicien
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#A1A5B7] italic">Traitée par {{ $demande->admin->name ?? 'Admin' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-[#A1A5B7] italic">
                                    Aucune demande de réaffectation pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($demandes->hasPages())
                <div class="px-6 py-4 border-t border-[#EFF2F5]">
                    {{ $demandes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
