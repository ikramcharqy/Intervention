<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nouvel Utilisateur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block font-medium">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="prenom" class="block font-medium">Prénom <span class="text-red-500">*</span></label>
                            <input type="text" name="prenom" id="prenom" required value="{{ old('prenom') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block font-medium">E-mail <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="telephone" class="block font-medium">Téléphone <span class="text-red-500">*</span></label>
                            <input type="text" name="telephone" id="telephone" required value="{{ old('telephone') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('telephone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="adresse" class="block font-medium">Adresse</label>
                            <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('adresse') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password" class="block font-medium">Mot de passe <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block font-medium">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        </div>

                        <div>
                            <label for="photo" class="block font-medium">Photo de profil</label>
                            <input type="file" name="photo" id="photo" class="w-full text-sm">
                            @error('photo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-medium mb-2">Rôles <span class="text-red-500">*</span></label>
                            <div class="space-y-1">
                                @foreach ($roles as $r)
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="roles[]" value="{{ $r->name }}" @checked(is_array(old('roles')) && in_array($r->name, old('roles')))>
                                        <span class="ml-2">{{ $r->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('roles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer</button>
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
