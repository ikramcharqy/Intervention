@props(['items'])
{{-- En-tête KPI partagé par les 4 vues de Supervision Métier (Chantiers, Interventions,
     Emplacements, Comptes Clients) — un seul style de carte plutôt qu'une variante par page. --}}
<div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 lg:divide-x divide-[#E6E9F4] dark:divide-slate-800">
        @foreach($items as $kpi)
            <div class="flex items-center justify-between gap-4 p-7">
                <div class="min-w-0">
                    <p class="text-[14px] text-[#5A607F] dark:text-slate-400 mb-1">{{ $kpi['title'] }}</p>
                    <p class="text-[20px] font-bold text-[#131523] dark:text-slate-100 leading-[28px]">{{ $kpi['value'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-full flex items-center justify-center shrink-0 {{ $kpi['theme']['bg'] }} {{ $kpi['theme']['text'] }}">
                    <x-icon :name="$kpi['icon']" class="w-6 h-6" />
                </div>
            </div>
        @endforeach
    </div>
</div>
