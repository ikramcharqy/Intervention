<tr>
    <td class="font-mono text-sm">{{ $intervention->code_intervention }}</td>
    <td>{{ $intervention->typeIntervention->nom ?? '-' }}</td>
    <td>{{ $intervention->technicien ? trim($intervention->technicien->prenom.' '.$intervention->technicien->name) : 'Non assigné' }}</td>
    <td>
        <x-soft-badge :status="$intervention->statut" />
    </td>
    <td class="text-sm text-gray-500">{{ $intervention->date_prevue_debut?->format('d/m/Y H:i') ?? '-' }}</td>
    <td class="text-right">
        <a href="{{ route('interventions.show', $intervention) }}" class="text-indigo-600 hover:text-indigo-900">Consulter</a>
    </td>
</tr>
