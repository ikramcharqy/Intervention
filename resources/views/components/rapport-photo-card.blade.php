{{-- Étape 2.3 : une photo dont le fichier est introuvable sur le disque
     affiche désormais un espace de repli explicite ("Photo indisponible")
     au lieu d'être silencieusement omise — la carte reste dans la grille
     plutôt que de disparaître sans indication. --}}
@props(['photo', 'captionParDefaut'])

@php
    $path = storage_path('app/public/' . $photo->chemin);
    $exists = file_exists($path);
@endphp

<div class="photo-card">
    @if($exists)
        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($path)) }}">
    @else
        <div style="height: 90px; display: flex; align-items: center; justify-content: center; background: #f1f5f9; color: #94a3b8; font-size: 9px; border-radius: 4px; text-align: center;">
            Photo indisponible
        </div>
    @endif
    <div class="photo-caption">{{ $photo->description ?? $captionParDefaut }}</div>
</div>
