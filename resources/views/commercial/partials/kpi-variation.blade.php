@if($tile['comparable'])
    <p class="text-[11px] font-bold mt-1.5 {{ $tile['up'] ? 'text-emerald-600' : 'text-rose-600' }}">
        {{ $tile['up'] ? '+' : '' }}{{ $tile['pct'] }}% <span class="text-[#A1A5B7] font-medium">vs {{ $periodLabel }}</span>
    </p>
@elseif($tile['is_new'])
    <p class="text-[11px] font-bold mt-1.5">
        <span class="px-1.5 py-0.5 rounded bg-[#F5F8FA] text-[#5E6278]">Nouveau</span>
        <span class="text-[#A1A5B7] font-medium ml-1">aucune donnée sur {{ $periodLabel }}</span>
    </p>
@else
    <p class="text-[11px] text-[#A1A5B7] font-medium mt-1.5">Aucune donnée comparable</p>
@endif
