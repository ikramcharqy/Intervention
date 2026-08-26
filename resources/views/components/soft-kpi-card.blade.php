@props([
    'title'       => '',
    'value'       => '0',
    'subtitle'    => '',
    'icon'        => 'fas fa-chart-line',
    'gradient'    => 'primary',
    'trend'       => '',
    'trendUp'     => true,
    'badgeText'   => '',
    'badgeType'   => 'success',
])

@php
    $iconThemes = [
        'primary' => [
            'bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        ],
        'info' => [
            'bg' => 'bg-sky-50 text-sky-600 border-sky-100',
        ],
        'success' => [
            'bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 text-amber-600 border-amber-100',
        ],
        'danger' => [
            'bg' => 'bg-rose-50 text-rose-600 border-rose-100',
        ],
        'dark' => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
        ],
    ];

    $theme = $iconThemes[$gradient] ?? $iconThemes['primary'];
@endphp

<div class="ui-card p-5 ui-card-hover flex items-center justify-between gap-4">
    <div class="min-w-0 flex-1">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 truncate">
            {{ $title }}
        </p>
        
        <div class="flex items-baseline gap-2.5 flex-wrap">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-none font-mono">
                {{ $value }}
            </h3>
            
            @if($trend)
                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $trendUp ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                    <span>{{ $trendUp ? '↑' : '↓' }}</span>
                    <span>{{ $trend }}</span>
                </span>
            @endif

            @if($badgeText)
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold {{ $badgeType === 'danger' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                    {{ $badgeText }}
                </span>
            @endif
        </div>

        @if($subtitle)
            <p class="text-xs text-slate-500 mt-2 font-medium truncate">{{ $subtitle }}</p>
        @endif
    </div>

    <!-- Icon Container -->
    <div class="w-11 h-11 rounded-xl flex items-center justify-center border shrink-0 shadow-xs {{ $theme['bg'] }}">
        <i class="{{ $icon }} text-base"></i>
    </div>
</div>
