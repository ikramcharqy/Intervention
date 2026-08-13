<x-commercial-layout>
    <x-slot name="header">Modifier Prospect</x-slot>

    <div style="max-width: 800px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div>
                <h2 style="font-size:18px; font-weight:700; color:#181c32;">Modifier : {{ $prospect->nom_entreprise }}</h2>
                <p style="font-size:12px; color:#a1a5b7; margin-top:3px;">Mettez à jour les informations du prospect</p>
            </div>
            <a href="{{ route('prospects.show', $prospect) }}" class="kt-btn kt-btn-light">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
        </div>

        <div class="kt-card">
            <div class="kt-card-body">
                <form action="{{ route('prospects.update', $prospect) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div class="kt-form-group">
                            <label class="kt-form-label">Nom de l'entreprise <span class="required">*</span></label>
                            <input type="text" name="nom_entreprise" required value="{{ old('nom_entreprise', $prospect->nom_entreprise) }}" class="kt-form-control">
                            @error('nom_entreprise')<div class="kt-form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Nom du contact</label>
                            <input type="text" name="nom_contact" value="{{ old('nom_contact', $prospect->nom_contact) }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $prospect->email) }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone', $prospect->telephone) }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group" style="grid-column:1/-1;">
                            <label class="kt-form-label">Adresse</label>
                            <input type="text" name="adresse" value="{{ old('adresse', $prospect->adresse) }}" class="kt-form-control">
                        </div>

                        <div class="kt-form-group">
                            <label class="kt-form-label">Statut</label>
                            <select name="statut" class="kt-form-control">
                                <option value="Nouveau"     {{ old('statut', $prospect->statut) == 'Nouveau'     ? 'selected' : '' }}>Nouveau</option>
                                <option value="Qualifié"    {{ old('statut', $prospect->statut) == 'Qualifié'    ? 'selected' : '' }}>Qualifié</option>
                                <option value="Négociation" {{ old('statut', $prospect->statut) == 'Négociation' ? 'selected' : '' }}>Négociation</option>
                                <option value="Converti"    {{ old('statut', $prospect->statut) == 'Converti'    ? 'selected' : '' }}>Converti</option>
                                <option value="Perdu"       {{ old('statut', $prospect->statut) == 'Perdu'       ? 'selected' : '' }}>Perdu</option>
                            </select>
                        </div>

                        <div class="kt-form-group" style="grid-column:1/-1;">
                            <label class="kt-form-label">Observations</label>
                            <textarea name="observations" rows="4" class="kt-form-control">{{ old('observations', $prospect->observations) }}</textarea>
                        </div>
                    </div>

                    <hr class="kt-separator" style="margin:8px 0 20px;">

                    <div style="display:flex; justify-content:flex-end; gap:10px;">
                        <a href="{{ route('prospects.show', $prospect) }}" class="kt-btn kt-btn-light">Annuler</a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-commercial-layout>
