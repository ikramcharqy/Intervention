<x-client-layout>
    <x-slot name="header">Mon Profil</x-slot>

    @if(session('success'))
        <div class="kt-alert kt-alert-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('success_password'))
        <div class="kt-alert kt-alert-success">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success_password') }}
        </div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">

        <!-- Informations Personnelles -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Informations Personnelles</div>
            </div>
            <div class="kt-card-body">
                <form action="{{ route('client.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="kt-form-group">
                        <label class="kt-form-label">Nom</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="kt-form-control">
                        @error('name')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required class="kt-form-control">
                        @error('prenom')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Adresse Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="kt-form-control">
                        @error('email')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="kt-form-control">
                        @error('telephone')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" class="kt-form-control">
                        @error('adresse')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                        <button type="submit" class="kt-btn kt-btn-success">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modification du Mot de Passe -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Changer le Mot de Passe</div>
            </div>
            <div class="kt-card-body">
                <form action="{{ route('client.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="kt-form-group">
                        <label class="kt-form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" required class="kt-form-control">
                        @error('current_password')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" required class="kt-form-control">
                        @error('password')<div class="kt-form-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="kt-form-group">
                        <label class="kt-form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation" required class="kt-form-control">
                    </div>

                    <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                        <button type="submit" class="kt-btn kt-btn-primary">Modifier le mot de passe</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-client-layout>
