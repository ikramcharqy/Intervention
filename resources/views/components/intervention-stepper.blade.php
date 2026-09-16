@props(['status' => ''])

@php
    // Mapping d'affichage uniquement (comme x-soft-badge) : ne modifie ni les
    // statuts réels ni transitionsAutorisees(). Les statuts opérationnels annexes
    // (Suspendue, Reportee, ClientAbsent...) sont rattachés à l'étape "En cours"
    // la plus proche pour rester lisible côté Client.
    $etapes = ['Planifiée', 'Acceptée', 'En cours', 'Formulaire rempli', 'Terminée'];

    $etapeParStatut = [
        'Demande' => 0, 'Planifiee' => 0, 'Planifiée' => 0, 'Affectee' => 0, 'Affectée' => 0,
        'En attente reafectation' => 0,
        'Acceptee' => 1, 'Acceptée' => 1,
        'En cours' => 2, 'Suspendue' => 2, 'Reportee' => 2, 'Reportée' => 2,
        'Partiellement realisee' => 2, 'Client absent' => 2, 'Materiel manquant' => 2,
        'Deuxieme visite requise' => 2, 'En attente validation' => 2, 'Rejetee' => 2, 'Rejetée' => 2,
        'Rouverte' => 2,
        'Formulaire rempli' => 3,
        'Terminee' => 4, 'Terminée' => 4, 'Validee' => 4, 'Validée' => 4,
    ];

    $estAnnulee = in_array($status, ['Annulee', 'Annulée'], true);
    $etapeActuelle = $etapeParStatut[$status] ?? 0;
@endphp

<div class="w-full">
    @if($estAnnulee)
        <div class="flex items-center gap-2.5 p-3.5 rounded-xl bg-slate-100 text-slate-500 text-xs font-bold">
            <i class="fas fa-ban"></i> Intervention annulée — le cycle de progression standard ne s'applique pas.
        </div>
    @else
        <div class="flex items-center">
            @foreach($etapes as $i => $label)
                <div class="flex items-center {{ $i < count($etapes) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center gap-1.5 shrink-0">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-extrabold border-2 transition
                            {{ $i < $etapeActuelle ? 'bg-emerald-500 border-emerald-500 text-white' : ($i === $etapeActuelle ? 'bg-white border-emerald-500 text-emerald-600 shadow-sm' : 'bg-white border-slate-200 text-slate-300') }}">
                            @if($i < $etapeActuelle)
                                <i class="fas fa-check text-[10px]"></i>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <span class="text-[10px] font-bold text-center max-w-[80px] {{ $i <= $etapeActuelle ? 'text-slate-700' : 'text-slate-300' }}">{{ $label }}</span>
                    </div>
                    @if($i < count($etapes) - 1)
                        <div class="flex-1 h-0.5 mx-1 mb-4 {{ $i < $etapeActuelle ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
