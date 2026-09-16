{{-- Champs "Informations professionnelles" : inclus dans le <form> unique de profile/edit.blade.php --}}
@php
    $roleName = $user->roles->pluck('name')->first() ?? 'Sans rôle';
    $badgeColor = match($roleName) {
        'Super Admin' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20',
        'admin', 'Administrateur' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
        'Commercial' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-500/20',
        'technicien', 'Technicien' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
        'Client' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20',
        default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
    };
    $peutModifierDateEntree = auth()->user()->hasAnyRole(['Super Admin', 'admin']);
@endphp

<div class="mb-6">
    <x-input-label :value="__('Rôle')" />
    <div class="mt-1">
        <span class="inline-block px-3 py-1 text-xs font-bold rounded-lg border {{ $badgeColor }}">{{ $roleName }}</span>
    </div>
</div>

{{-- Poste / Fonction --}}
<div>
    <x-input-label for="poste" :value="__('Poste / Fonction')" />
    <x-text-input id="poste" name="poste" type="text" class="mt-1 block w-full" :value="old('poste', $user->poste)" placeholder="Ex: Commercial Senior" />
    <x-input-error class="mt-2" :messages="$errors->get('poste')" />
</div>

{{-- Téléphone professionnel --}}
<div class="mt-4">
    <x-input-label for="telephone" :value="__('Téléphone professionnel')" />
    <x-text-input id="telephone" name="telephone" type="text" class="mt-1 block w-full" :value="old('telephone', $user->telephone)" required autocomplete="tel" />
    <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    {{-- Département / Équipe --}}
    <div>
        <x-input-label for="departement" :value="__('Département / Équipe')" />
        <x-text-input id="departement" name="departement" type="text" class="mt-1 block w-full" :value="old('departement', $user->departement)" placeholder="Optionnel" />
        <x-input-error class="mt-2" :messages="$errors->get('departement')" />
    </div>

    {{-- Zone géographique --}}
    <div>
        <x-input-label for="zone_geographique" :value="__('Zone géographique assignée')" />
        <x-text-input id="zone_geographique" name="zone_geographique" type="text" class="mt-1 block w-full" :value="old('zone_geographique', $user->zone_geographique)" placeholder="Ex: Casablanca-Settat" />
        <x-input-error class="mt-2" :messages="$errors->get('zone_geographique')" />
    </div>
</div>

{{-- Date d'entrée : lecture seule sauf Admin/Super Admin --}}
<div class="mt-4">
    <x-input-label for="date_entree" :value="__('Date d\'entrée dans l\'entreprise')" />
    <x-text-input id="date_entree" name="date_entree" type="date" class="mt-1 block w-full"
        :value="old('date_entree', $user->date_entree?->format('Y-m-d'))"
        @disabled(! $peutModifierDateEntree) />
    @unless($peutModifierDateEntree)
        <p class="text-[11px] text-gray-400 mt-1">Modifiable uniquement par un administrateur.</p>
    @endunless
    <x-input-error class="mt-2" :messages="$errors->get('date_entree')" />
</div>
