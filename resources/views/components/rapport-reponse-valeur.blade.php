{{-- Rendu de la valeur d'une Reponse selon Question::type_reponse — logique
     partagée entre le web (Admin/Commercial/Super Admin/Client, via
     x-rapport-formulaire) et le PDF, pour ne plus avoir deux implémentations
     divergentes de ce dispatch (le PDF n'avait auparavant ni Checkbox, ni
     Radio/Liste, ni Document : ces réponses tombaient dans son cas générique
     et affichaient un "-"). --}}
@props(['reponse', 'question', 'pdfMode' => false])

@if(in_array($question->type_reponse, ['Photo', 'Signature', 'Document']))
    @if($reponse->reponse_fichier)
        @php $ext = pathinfo($reponse->reponse_fichier, PATHINFO_EXTENSION); @endphp
        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
            @if($pdfMode)
                @php
                    $filePath = storage_path('app/public/' . $reponse->reponse_fichier);
                    $exists = file_exists($filePath);
                @endphp
                @if($exists)
                    {{-- Étape 5 : agrandi (90px → 160px) pour rester exploitable
                         à l'impression — la résolution réelle de l'image
                         embarquée (capture technicien) est inchangée, seule sa
                         taille d'affichage dans le PDF était trop petite. --}}
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($filePath)) }}" style="max-height: 160px; max-width: 100%; border-radius: 3px; border: 1px solid #cbd5e1;">
                @else
                    <span style="color:#94a3b8;">[Fichier enregistré : {{ basename($reponse->reponse_fichier) }}]</span>
                @endif
            @else
                <img src="{{ Storage::url($reponse->reponse_fichier) }}" class="max-w-xs h-auto rounded-[6px] border border-[#E6E9F4]">
            @endif
        @else
            @if($pdfMode)
                <span>[Document joint : {{ basename($reponse->reponse_fichier) }}]</span>
            @else
                <a href="{{ Storage::url($reponse->reponse_fichier) }}" target="_blank" class="text-xs text-[#1E5EFF] hover:underline">
                    Télécharger le fichier ({{ basename($reponse->reponse_fichier) }})
                </a>
            @endif
        @endif
    @else
        <span class="{{ $pdfMode ? '' : 'text-xs text-[#A1A7C4]' }}" @if($pdfMode) style="color:#94a3b8;" @endif>Aucun fichier</span>
    @endif
@elseif($question->type_reponse === 'OuiNon')
    @if($pdfMode)
        @php $isOui = in_array(strtolower($reponse->reponse_texte ?? ''), ['1', 'oui', 'true']); @endphp
        <span style="font-weight: bold; color: {{ $isOui ? '#059669' : '#dc2626' }};">
            {{ $isOui ? '✔ OUI / CONFORME' : '✖ NON / ANOMALIE' }}
        </span>
    @else
        <span class="ds-badge ds-badge-sm {{ $reponse->reponse_texte === '1' ? 'ds-badge-light-success' : 'ds-badge-light-danger' }}">
            {{ $reponse->reponse_texte === '1' ? 'Oui' : 'Non' }}
        </span>
    @endif
@elseif($question->type_reponse === 'Checkbox')
    @php
        $choices = \App\Models\ChoixQuestion::whereIn('id', is_array($reponse->reponse_texte) ? $reponse->reponse_texte : explode(',', $reponse->reponse_texte ?? ''))->pluck('valeur')->toArray();
    @endphp
    <span class="{{ $pdfMode ? '' : 'text-xs text-[#5A607F]' }}">{{ implode(', ', $choices) }}</span>
@elseif($question->type_reponse === 'Radio' || $question->type_reponse === 'Liste')
    <span class="{{ $pdfMode ? '' : 'text-xs text-[#5A607F]' }}">{{ $reponse->choixQuestion->valeur ?? $reponse->reponse_texte }}</span>
@else
    <span class="{{ $pdfMode ? '' : 'text-xs text-[#5A607F]' }}">{{ $reponse->reponse_texte ?? $reponse->reponse_nombre ?? '-' }}</span>
@endif
