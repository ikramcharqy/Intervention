<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormulaireRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type_intervention_id' => [
                'required',
                'integer',
                Rule::exists('type_interventions', 'id')->where('is_active', true),
                // Un seul formulaire par TypeIntervention
                Rule::unique('formulaires', 'type_intervention_id'),
            ],
            'nom'         => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type_intervention_id.required' => 'Veuillez sélectionner un type d\'intervention.',
            'type_intervention_id.exists'   => 'Le type d\'intervention sélectionné n\'existe pas ou est inactif.',
            'type_intervention_id.unique'   => 'Un formulaire existe déjà pour ce type d\'intervention.',
            'nom.required'                  => 'Le nom du formulaire est obligatoire.',
        ];
    }

    public function attributes(): array
    {
        return [
            'type_intervention_id' => 'type d\'intervention',
            'nom'                  => 'nom du formulaire',
        ];
    }
}
