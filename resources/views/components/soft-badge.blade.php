@props([
    'status' => '',
])

@php
    $statusMap = [
        'Planifiée'                 => ['gradient' => 'background-image: linear-gradient(310deg, #627594 0%, #a8b8d0 100%);', 'text' => 'Planifiée'],
        'Affectée'                  => ['gradient' => 'background-image: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%);', 'text' => 'Affectée'],
        'Acceptee'                  => ['gradient' => 'background-image: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);', 'text' => 'Acceptée'],
        'En cours'                  => ['gradient' => 'background-image: linear-gradient(310deg, #f53939 0%, #fbcf33 100%);', 'text' => 'En Cours'],
        'Formulaire rempli'         => ['gradient' => 'background-image: linear-gradient(310deg, #7928ca 0%, #cb0c9f 100%);', 'text' => 'Formulaire Rempli'],
        'Terminee'                  => ['gradient' => 'background-image: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);', 'text' => 'Terminée'],
        'Validee'                   => ['gradient' => 'background-image: linear-gradient(310deg, #17ad37 0%, #98ec2d 100%);', 'text' => 'Validée & Clôturée'],
        'Annulee'                   => ['gradient' => 'background-image: linear-gradient(310deg, #ea0606 0%, #ff667c 100%);', 'text' => 'Annulée'],
        'En attente reafectation'   => ['gradient' => 'background-image: linear-gradient(310deg, #ea0606 0%, #ff667c 100%);', 'text' => 'Refus Technicien'],
        'Suspendue'                 => ['gradient' => 'background-image: linear-gradient(310deg, #627594 0%, #a8b8d0 100%);', 'text' => 'En Pause'],
    ];

    $config = $statusMap[$status] ?? ['gradient' => 'background-image: linear-gradient(310deg, #627594 0%, #a8b8d0 100%);', 'text' => $status ?: 'Inconnu'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white shadow-xs uppercase tracking-wider" style="{{ $config['gradient'] }}">
    {{ $config['text'] }}
</span>
