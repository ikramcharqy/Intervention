<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormulaireRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $formulaire = $this->route('formulaire');

        return [
            'type_intervention_id' => [
                'required',
                'integer',
                Rule::exists('type_interventions', 'id')->where('is_active', true),
                Rule::unique('formulaires', 'type_intervention_id')->ignore($formulaire),
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
            'type_intervention_id.unique'   => 'Un autre formulaire existe déjà pour ce type d\'intervention.',
            'nom.required'                  => 'Le nom du formulaire est obligatoire.',
        ];
    }
}
