<x-client-layout>
    <x-slot name="header">Notifications</x-slot>

    @php
        $groupes = [
            'tous' => 'Toutes',
            'interventions' => 'Interventions',
            'rapports' => 'Rapports',
            'demandes' => 'Demandes',
        ];

        $grouperParJour = function ($items) {
            return $items->groupBy(function ($n) {
                if ($n->created_at->isToday()) return "Aujourd'hui";
                if ($n->created_at->isYesterday()) return 'Hier';
                return 'Plus tôt';
            });
        };
        $sections = $grouperParJour(collect($notifications->items()));
        $ordreSections = ["Aujourd'hui", 'Hier', 'Plus tôt'];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($groupes as $value => $label)
                    <a href="{{ route('client.notifications.index', ['type' => $value]) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $typeFiltre === $value ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('client.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white shadow-sm hover:shadow text-slate-600 text-xs font-bold transition">
                        <i class="fas fa-check-double text-emerald-500"></i> Tout marquer comme lu
                        <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-rose-500 text-white text-[10px] font-extrabold">{{ $unreadCount }}</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            @forelse($ordreSections as $section)
                @continue(!isset($sections[$section]))
                <div class="px-6 pt-5 pb-2">
                    <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">{{ $section }}</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($sections[$section] as $notification)
                        @php
                            $data = $notification->data;
                            $nonLu = is_null($notification->read_at);
                        @endphp
                        <a href="{{ route('client.notifications.open', $notification->id) }}"
                           class="flex items-start gap-3 px-6 py-4 transition {{ $nonLu ? 'bg-emerald-50/40 hover:bg-emerald-50' : 'hover:bg-slate-50' }}">
                            <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $nonLu ? 'bg-emerald-500' : 'bg-transparent' }}"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm {{ $nonLu ? 'font-extrabold text-slate-900' : 'font-semibold text-slate-600' }}">{{ $data['titre'] ?? 'Notification' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $data['message'] ?? '' }}</p>
                                <span class="inline-block mt-1.5 text-xs font-bold text-emerald-600">Voir le détail</span>
                            </div>
                            <div class="text-[11px] text-slate-400 shrink-0" title="{{ $notification->created_at->format('d/m/Y H:i') }}">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @empty
                <div class="py-16 text-center text-slate-400 italic">Aucune notification pour le moment.</div>
            @endforelse

            @if($notifications->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
