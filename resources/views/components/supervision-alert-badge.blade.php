@props(['label'])
{{-- Badge "dormant/sans activité" partagé par les 4 vues de Supervision Métier (chantiers
     sans intervention, emplacements jamais utilisés, clients sans activité) — un seul
     composant visuel plutôt qu'une variante locale par page. --}}
<span {{ $attributes->merge(['class' => 'block mt-1 w-fit px-2 py-0.5 text-[9px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full']) }}>{{ $label }}</span>
