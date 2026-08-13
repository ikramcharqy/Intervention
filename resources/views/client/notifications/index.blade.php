<x-client-layout>
    <x-slot name="header">Notifications & Événements</x-slot>

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Historique des Événements Client</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">Suivi de l'activité de vos chantiers</div>
            </div>
        </div>

        <div style="padding: 24px;">
            @forelse($notifications as $notif)
                <div style="display:flex; align-items:flex-start; gap:16px; padding:16px; border-radius:12px; background:#f5f8fa; margin-bottom:12px; border-left:4px solid #3e97ff;">
                    <div style="width:40px; height:40px; border-radius:10px; background:white; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                        @if($notif['type'] === 'planifiee')
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#3e97ff" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif($notif['type'] === 'commencee')
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#ffc700" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($notif['type'] === 'terminee')
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#50cd89" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#7239ea" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                            <div style="font-size:14px; font-weight:700; color:#181c32;">{{ $notif['title'] }}</div>
                            <span style="font-size:11px; color:#a1a5b7;">{{ $notif['date']->format('d/m/Y H:i') }}</span>
                        </div>
                        <p style="font-size:13px; color:#5e6278; margin-bottom:8px; line-height:1.5;">{{ $notif['message'] }}</p>
                        <a href="{{ $notif['link'] }}" class="kt-btn kt-btn-light-primary kt-btn-sm" style="font-size:11px; padding:4px 10px;">Voir le détail</a>
                    </div>
                </div>
            @empty
                <div class="kt-empty-state">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p>Aucune notification enregistrée pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>

</x-client-layout>
