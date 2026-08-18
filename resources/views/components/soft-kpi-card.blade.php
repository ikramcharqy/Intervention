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
    $gradientMap = [
        'primary'   => 'linear-gradient(310deg, #4c1d95 0%, #7c3aed 50%, #a855f7 100%)',
        'accent'    => 'linear-gradient(310deg, #7c3aed 0%, #ec4899 100%)',
        'info'      => 'linear-gradient(310deg, #1e40af 0%, #3b82f6 100%)',
        'success'   => 'linear-gradient(310deg, #065f46 0%, #10b981 100%)',
        'warning'   => 'linear-gradient(310deg, #92400e 0%, #f59e0b 100%)',
        'danger'    => 'linear-gradient(310deg, #7f1d1d 0%, #ef4444 100%)',
        'dark'      => 'linear-gradient(310deg, #0f0f1a 0%, #1e1e33 100%)',
        'secondary' => 'linear-gradient(310deg, #1e293b 0%, #334155 100%)',
    ];
    $bg = $gradientMap[$gradient] ?? $gradientMap['primary'];
@endphp

<div style="background:#16162a; border:1px solid rgba(139,92,246,0.12); border-radius:1rem;
            box-shadow:0 4px 24px 0 rgba(0,0,0,0.45); padding:1.25rem;
            display:flex; align-items:center; justify-content:space-between; gap:1rem;
            transition:all 0.2s; cursor:default;"
     onmouseover="this.style.borderColor='rgba(139,92,246,0.35)'; this.style.transform='translateY(-3px)';"
     onmouseout="this.style.borderColor='rgba(139,92,246,0.12)'; this.style.transform='translateY(0)';">

    <!-- Texte KPI -->
    <div style="min-width:0;">
        <p style="font-size:0.58rem; font-weight:800; text-transform:uppercase; letter-spacing:0.12em;
                  color:#64748b; margin:0 0 0.375rem;">{{ $title }}</p>
        <div style="display:flex; align-items:baseline; gap:0.5rem; flex-wrap:wrap;">
            <h3 style="font-size:1.75rem; font-weight:900; color:#e2e8f0; line-height:1; margin:0;">
                {{ $value }}
            </h3>
            @if($trend)
                <span style="font-size:0.62rem; font-weight:700; padding:0.15rem 0.5rem; border-radius:9999px;
                             {{ $trendUp
                                ? 'background:rgba(16,185,129,0.15); color:#6ee7b7; border:1px solid rgba(16,185,129,0.25);'
                                : 'background:rgba(239,68,68,0.15); color:#fca5a5; border:1px solid rgba(239,68,68,0.25);' }}">
                    {{ $trendUp ? '▲' : '▼' }} {{ $trend }}
                </span>
            @endif
            @if($badgeText)
                <span style="font-size:0.62rem; font-weight:700; padding:0.15rem 0.5rem; border-radius:9999px;
                             {{ $badgeType === 'danger'
                                ? 'background:rgba(239,68,68,0.15); color:#fca5a5;'
                                : 'background:rgba(16,185,129,0.15); color:#6ee7b7;' }}">
                    {{ $badgeText }}
                </span>
            @endif
        </div>
        @if($subtitle)
            <p style="font-size:0.68rem; color:#64748b; margin:0.375rem 0 0;">{{ $subtitle }}</p>
        @endif
    </div>

    <!-- Icône Gradient -->
    <div style="width:3rem; height:3rem; border-radius:0.875rem; background-image:{{ $bg }};
                display:flex; align-items:center; justify-content:center; color:#fff;
                font-size:1.05rem; flex-shrink:0; box-shadow:0 4px 16px rgba(0,0,0,0.3);">
        <i class="{{ $icon }}"></i>
    </div>
</div>
