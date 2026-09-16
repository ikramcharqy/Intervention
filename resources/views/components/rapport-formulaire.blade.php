@props(['rapport', 'pdfMode' => false])

@if($rapport->reponses->isNotEmpty())
    @if($pdfMode)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Point de Contrôle / Question</th>
                    <th style="width: 55%;">Constat / Réponse Technicien</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rapport->reponses as $reponse)
                    @php $question = $reponse->question; @endphp
                    @continue(!$question || $question->type_reponse === 'Materiaux')
                    <tr>
                        <td><strong>{{ $question->question }}</strong></td>
                        <td><x-rapport-reponse-valeur :reponse="$reponse" :question="$question" :pdf-mode="true" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="space-y-3">
            @foreach($rapport->reponses as $reponse)
                @php $question = $reponse->question; @endphp
                @if($question && $question->type_reponse !== 'Materiaux')
                    <div class="p-4 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4]">
                        <p class="text-xs font-bold text-[#131523] mb-2">{{ $question->question }}</p>
                        <x-rapport-reponse-valeur :reponse="$reponse" :question="$question" :pdf-mode="false" />
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@else
    @if($pdfMode)
        <p style="color:#94a3b8; font-style:italic; font-size:11px;">Aucune réponse de formulaire enregistrée pour cette intervention.</p>
    @else
        <p class="text-xs text-[#A1A7C4] italic">Aucune réponse de formulaire enregistrée pour cette intervention.</p>
    @endif
@endif
