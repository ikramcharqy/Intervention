@php
/**
 * Partial: actions disponibles selon statut et rôle
 * Utilisation: @include('interventions._actions', ['intervention' => $intervention])
 */
@endphp

<div class="intervention-actions">
    {{-- Technicien: accepter / refuser --}}
    @can('accept', $intervention)
        <form method="POST" action="{{ route('api.interventions.accept', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-success">Accepter</button>
        </form>
    @endcan

    @can('refuse', $intervention)
        <form method="POST" action="{{ route('api.interventions.refuse', $intervention) }}">
            @csrf
            <input type="hidden" name="motif" value="Refus depuis l'interface mobile">
            <button type="submit" class="btn btn-danger">Refuser</button>
        </form>
    @endcan

    {{-- Technicien: démarrer / suspendre / reprendre --}}
    @can('start', $intervention)
        <form method="POST" action="{{ route('api.interventions.start', $intervention) }}">
            @csrf
            <input type="hidden" name="mode" value="{{ \App\Models\Intervention::MODE_MANUEL }}">
            <button type="submit" class="btn btn-primary">Démarrer</button>
        </form>
    @endcan

    @can('suspend', $intervention)
        <form method="POST" action="{{ route('api.interventions.suspend', $intervention) }}">
            @csrf
            <input type="hidden" name="motif" value="Pause technique">
            <button type="submit" class="btn btn-warning">Mettre en pause</button>
        </form>
    @endcan

    @can('resume', $intervention)
        <form method="POST" action="{{ route('api.interventions.resume', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-primary">Reprendre</button>
        </form>
    @endcan

    {{-- Technicien: soumettre le formulaire --}}
    @can('submitForm', $intervention)
        <form method="POST" action="{{ route('api.interventions.submitForm', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-outline-success">Soumettre formulaire</button>
        </form>
    @endcan

    {{-- Admin: réaffecter / report / close / reopen / cancel --}}
    @can('reassign', $intervention)
        <a href="{{ route('interventions.reassign.form', $intervention) }}" class="btn btn-outline-secondary">Réaffecter</a>
    @endcan

    @can('reschedule', $intervention)
        <a href="{{ route('interventions.report.form', $intervention) }}" class="btn btn-outline-secondary">Reporter</a>
    @endcan

    @can('close', $intervention)
        <form method="POST" action="{{ route('api.interventions.close', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-success">Clôturer</button>
        </form>
    @endcan

    @can('reopen', $intervention)
        <form method="POST" action="{{ route('api.interventions.reopen', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Rouvrir</button>
        </form>
    @endcan

    @can('cancel', $intervention)
        <form method="POST" action="{{ route('api.interventions.cancel', $intervention) }}">
            @csrf
            <button type="submit" class="btn btn-danger">Annuler</button>
        </form>
    @endcan
</div>
