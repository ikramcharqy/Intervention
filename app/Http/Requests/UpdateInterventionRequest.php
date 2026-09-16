<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInterventionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $intervention = $this->route('intervention');

        return [
            'code_intervention' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('interventions', 'code_intervention')->ignore($intervention),
            ],
            'chantier_id' => [
                'required',
                'integer',
                Rule::exists('chantiers', 'id')->where('is_active', true),
            ],
            'emplacement_id' => [
                'nullable',
                'integer',
                // L'emplacement doit être actif ET appartenir au chantier sélectionné
                Rule::exists('emplacements', 'id')
                    ->where('is_active', true)
                    ->where('chantier_id', $this->input('chantier_id')),
            ],
            'technicien_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('is_active', true),
                function ($attribute, $value, $fail) {
                    $user = \App\Models\User::find($value);
                    if (!$user || !$user->hasRole('technicien')) {
                        $fail('L\'utilisateur sélectionné n\'a pas le rôle Technicien.');
                    }
                },
            ],
            'type_intervention_id' => [
                'required',
                'integer',
                Rule::exists('type_interventions', 'id')->where('is_active', true),
            ],
            'priorite' => [
                'required',
                Rule::in(['Faible', 'Normale', 'Haute', 'Urgente']),
            ],
            // Le statut n'est pas modifiable via ce formulaire —
            // les transitions se font via les actions dédiées du controller.
            'date_prevue_debut'  => ['required', 'date'],
            'date_prevue_fin'    => ['required', 'date', 'after:date_prevue_debut'],
            'date_reelle_debut'  => ['nullable', 'date'],
            'date_reelle_fin'    => ['nullable', 'date', 'after_or_equal:date_reelle_debut'],
            'duree_prevue'       => ['nullable', 'integer', 'min:1', 'max:99999'],
            'duree_reelle'       => ['nullable', 'integer', 'min:0', 'max:99999'],
            'pourcentage_global' => ['nullable', 'integer', 'min:0', 'max:100'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'observations'       => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'code_intervention.required'    => 'Le code intervention est obligatoire.',
            'code_intervention.unique'      => 'Ce code intervention est déjà utilisé par une autre intervention.',
            'code_intervention.regex'       => 'Le code ne peut contenir que des lettres majuscules, des chiffres et des tirets.',
            'chantier_id.required'          => 'Veuillez sélectionner un chantier.',
            'chantier_id.exists'            => 'Le chantier sélectionné n\'existe pas ou est inactif.',
            'emplacement_id.exists'         => 'L\'emplacement sélectionné n\'existe pas, est inactif ou n\'appartient pas au chantier sélectionné.',
            'technicien_id.required'        => 'Veuillez sélectionner un technicien.',
            'technicien_id.exists'          => 'Le technicien sélectionné n\'existe pas ou est inactif.',
            'type_intervention_id.required' => 'Veuillez sélectionner un type d\'intervention.',
            'type_intervention_id.exists'   => 'Le type d\'intervention sélectionné n\'existe pas ou est inactif.',
            'priorite.required'             => 'La priorité est obligatoire.',
            'priorite.in'                   => 'La priorité doit être : Faible, Normale, Haute ou Urgente.',
            'date_prevue_debut.required'    => 'La date de début prévue est obligatoire.',
            'date_prevue_fin.required'      => 'La date de fin prévue est obligatoire.',
            'date_prevue_fin.after'         => 'La date de fin prévue doit être postérieure à la date de début.',
            'date_reelle_fin.after_or_equal' => 'La date de fin réelle doit être postérieure ou égale à la date de début réelle.',
            'pourcentage_global.min'        => 'Le pourcentage doit être compris entre 0 et 100.',
            'pourcentage_global.max'        => 'Le pourcentage doit être compris entre 0 et 100.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code_intervention'    => 'code intervention',
            'chantier_id'          => 'chantier',
            'emplacement_id'       => 'emplacement',
            'technicien_id'        => 'technicien',
            'type_intervention_id' => 'type d\'intervention',
            'priorite'             => 'priorité',
            'date_prevue_debut'    => 'date de début prévue',
            'date_prevue_fin'      => 'date de fin prévue',
            'date_reelle_debut'    => 'date de début réelle',
            'date_reelle_fin'      => 'date de fin réelle',
            'duree_prevue'         => 'durée prévue',
            'duree_reelle'         => 'durée réelle',
            'pourcentage_global'   => 'avancement global',
            'description'          => 'description',
            'observations'         => 'observations',
        ];
    }
}
