<x-app-layout>
    <div class="space-y-6">
        <!-- Titre + Badge -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Arbitrage — Refus Techniciens &amp; Réaffectation</h1>
                <p class="text-xs text-[#5A607F] mt-1">Gestion des demandes de réaffectation suite aux refus motivés transmis par les techniciens</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-[4px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-[#F0142F] {{ $demandes->total() > 0 ? 'animate-pulse' : '' }}"></span>
                {{ $demandes->total() }} demande(s) enregistrée(s)
            </span>
        </div>

        <!-- Alertes session -->
        @if(session('success'))
            <div class="p-4 rounded-[6px] bg-[#E3FBF0] border border-[#C4F8E2] text-[#06A561] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-[#06A561] text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-[6px] bg-[#FDE3E6] border border-[#F8C4CA] text-[#F0142F] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-[#F0142F] text-sm shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Table -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-[#E6E9F4]">
                <i class="fas fa-users text-[#F0142F]"></i>
                <h3 class="text-sm font-bold text-[#131523]">Demandes de Réaffectation en attente d'Arbitrage Admin</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Intervention</th>
                            <th>Technicien Réclamant</th>
                            <th>Motif du Refus</th>
                            <th>Statut Demande</th>
                            <th>Émise le</th>
                            <th class="text-right">Décision &amp; Arbitrage Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            <tr>
                                <td>
                                    <a href="{{ route('interventions.show', $demande->intervention_id) }}" class="inline-flex items-center gap-2 group">
                                        <span class="font-bold text-xs text-[#1E5EFF] bg-[#EAF0FF] px-2 py-0.5 rounded border border-[#D9E4FF] group-hover:bg-[#D9E4FF] transition">
                                            #{{ $demande->intervention->code_intervention ?? 'INT-'.$demande->intervention_id }}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-[4px] bg-[#F5F6FA] border border-[#E6E9F4] text-[#5A607F] text-[10px] font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($demande->technicien->name ?? 'NA', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-[#131523] truncate">{{ $demande->technicien->name ?? 'N/A' }} {{ $demande->technicien->prenom ?? '' }}</p>
                                            <p class="text-[11px] text-[#A1A7C4] truncate">{{ $demande->technicien->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="p-2.5 rounded-[6px] bg-[#FFF3DE] border border-[#FFE7B8] text-[#B98900] text-[11px] max-w-sm" title="{{ $demande->motif }}">
                                        <span class="font-bold">Motif :</span> "{{ $demande->motif }}"
                                    </div>
                                </td>
                                <td>
                                    @if($demande->statut === 'En attente')
                                        <span class="ds-badge ds-badge-sm ds-badge-light-warning inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#B98900] animate-pulse"></span>
                                            En attente
                                        </span>
                                    @elseif($demande->statut === 'Acceptee')
                                        <span class="ds-badge ds-badge-sm ds-badge-light-success">Acceptée par Admin</span>
                                    @else
                                        <span class="ds-badge ds-badge-sm ds-badge-light-danger">Refusée par Admin</span>
                                    @endif
                                </td>
                                <td class="text-xs text-[#5A607F] whitespace-nowrap">
                                    {{ $demande->created_at ? $demande->created_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="text-right">
                                    @if($demande->statut === 'En attente')
                                        <div class="flex items-center justify-end gap-2 flex-wrap">
                                            <form action="{{ route('demandes-reaffectation.traiter', $demande->id) }}" method="POST" class="flex items-center gap-1.5">
                                                @csrf
                                                <input type="hidden" name="action" value="accepter">
                                                <select name="nouveau_technicien_id" class="text-xs py-1.5 px-2 rounded-[4px] border border-[#E6E9F4] bg-white text-[#131523] focus:border-[#1E5EFF] focus:ring-0">
                                                    <option value="">-- Remettre en attente --</option>
                                                    @foreach($techniciens as $t)
                                                        @if($t->id !== $demande->technicien_id)
                                                            <option value="{{ $t->id }}">{{ $t->name }} {{ $t->prenom }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="ds-btn ds-btn-sm" style="background-color:#06A561; color:#fff;">
                                                    <i class="fas fa-check"></i> Accepter
                                                </button>
                                            </form>

                                            <form action="{{ route('demandes-reaffectation.traiter', $demande->id) }}" method="POST" onsubmit="return confirm('Confirmez-vous le refus de la demande ? Le technicien initial sera contraint d\'exécuter l\'intervention.');">
                                                @csrf
                                                <input type="hidden" name="action" value="refuser">
                                                <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm">
                                                    <i class="fas fa-times"></i> Forcer Technicien
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-[#A1A7C4] italic">Traitée par {{ $demande->admin->name ?? 'Admin' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="text-center py-16">
                                        <div class="w-14 h-14 rounded-full bg-[#E3FBF0] flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-check text-xl text-[#06A561]"></i>
                                        </div>
                                        <p class="text-sm font-bold text-[#131523]">Aucune demande de réaffectation</p>
                                        <p class="text-xs text-[#A1A7C4] mt-1">Aucun refus technicien en attente d'arbitrage pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($demandes->hasPages())
                <div class="px-6 py-4 border-t border-[#E6E9F4] flex items-center justify-between flex-wrap gap-3">
                    <p class="text-xs text-[#A1A7C4]">{{ $demandes->total() }} résultat(s)</p>
                    {{ $demandes->links() }}
                </div>
            @else
                <div class="px-6 py-4 border-t border-[#E6E9F4]">
                    <p class="text-xs text-[#A1A7C4]">{{ $demandes->total() }} résultat(s)</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
