@php
    $documentTitle = 'Fiche Chantier';
    $documentReference = $chantier->code_chantier ?? (string) $chantier->id;
@endphp
@extends('pdf.layout')

@section('meta')
    <strong>Chantier :</strong> {{ $chantier->nom }}<br>
    <strong>Ville :</strong> {{ $chantier->ville ?? '—' }}
@endsection

@section('content')
    <div class="section">
        <div class="section-header">Informations générales</div>
        <table class="data-table" style="border:none;">
            <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Adresse</td><td style="border:none;">{{ $chantier->adresse ?? '—' }}</td></tr>
            <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Type de local</td><td style="border:none;">{{ $chantier->type_local ?? '—' }}</td></tr>
            <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Responsable</td><td style="border:none;">{{ $chantier->responsable ?? '—' }}</td></tr>
            <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Téléphone</td><td style="border:none;">{{ $chantier->telephone_responsable ?? '—' }}</td></tr>
            <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Email</td><td style="border:none;">{{ $chantier->email_responsable ?? '—' }}</td></tr>
            @if($chantier->description)
                <tr><td style="border:none; width:160px; font-weight:bold; color:#64748b;">Description</td><td style="border:none;">{{ $chantier->description }}</td></tr>
            @endif
        </table>
    </div>

    @if($chantier->client && $chantier->client->contacts->isNotEmpty())
        <div class="section">
            <div class="section-header">Contacts de l'entreprise</div>
            <table class="data-table" style="border:none;">
                @foreach($chantier->client->contacts as $contact)
                    <tr>
                        <td style="border:none; width:220px; font-weight:bold; color:#64748b;">{{ trim($contact->prenom . ' ' . $contact->nom) }}{{ $contact->fonction ? ' ('.$contact->fonction.')' : '' }}</td>
                        <td style="border:none;">{{ $contact->telephone ?? '' }} {{ $contact->email ? '— '.$contact->email : '' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="section">
        <div class="section-header">Interventions récentes ({{ $chantier->interventions->count() }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Technicien</th>
                    <th>Date prévue</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chantier->interventions as $interv)
                    <tr>
                        <td>{{ $interv->code_intervention }}</td>
                        <td>{{ $interv->typeIntervention?->nom ?? '—' }}</td>
                        <td>{{ $interv->technicien?->name ?? 'Non assigné' }}</td>
                        <td>{{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : '—' }}</td>
                        <td>{{ $interv->statut }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Aucune intervention enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
