{{-- Champs "Informations personnelles" : inclus dans le <form> unique de profile/edit.blade.php --}}
<div class="flex items-center gap-4 mb-6" x-data="{ preview: null }">
    <div class="w-20 h-20 rounded-full overflow-hidden shrink-0 bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
        <template x-if="preview">
            <img :src="preview" class="w-full h-full object-cover" alt="Aperçu de la photo">
        </template>
        <template x-if="!preview">
            <div class="w-full h-full flex items-center justify-center">
                @if($user->photo)
                    <img src="{{ asset('storage/' . $user->photo) }}" class="w-full h-full object-cover" alt="Photo de profil">
                @else
                    <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">
                        {{ strtoupper(mb_substr($user->prenom ?? $user->name, 0, 1) . mb_substr($user->name, 0, 1)) }}
                    </span>
                @endif
            </div>
        </template>
    </div>
    <div>
        <label for="photo" class="cursor-pointer inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/50">
            {{ __('Changer la photo') }}
        </label>
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
            @change="const f = $event.target.files[0]; if (f) { preview = URL.createObjectURL(f); }">
        <p class="text-[11px] text-gray-400 mt-1">JPEG, PNG ou WebP, 5 Mo max.</p>
        <x-input-error class="mt-1" :messages="$errors->get('photo')" />
    </div>
</div>

{{-- Nom --}}
<div>
    <x-input-label for="name" :value="__('Nom')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="family-name" />
    <x-input-error class="mt-2" :messages="$errors->get('name')" />
</div>

{{-- Prénom --}}
<div class="mt-4">
    <x-input-label for="prenom" :value="__('Prénom')" />
    <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom', $user->prenom)" required autocomplete="given-name" />
    <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
</div>

{{-- Email --}}
<div class="mt-4">
    <x-input-label for="email" :value="__('Email')" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
    <x-input-error class="mt-2" :messages="$errors->get('email')" />
</div>

{{-- Adresse personnelle (optionnelle) --}}
<div class="mt-4">
    <x-input-label for="adresse" :value="__('Adresse personnelle')" />
    <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" :value="old('adresse', $user->adresse)" autocomplete="street-address" placeholder="Optionnel" />
    <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
</div>
