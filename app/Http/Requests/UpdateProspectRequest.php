<?php

namespace App\Http\Requests;

use App\Models\Prospect;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProspectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_prospect' => ['required', 'in:' . implode(',', Prospect::TYPES_PROSPECT)],
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'nom_contact' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255', 'required_without:telephone'],
            'telephone' => ['nullable', 'regex:/^0[5-7][0-9]{8}$/', 'required_without:email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'statut' => ['required', 'string'],
            'observations' => ['nullable', 'string'],
            'commercial_id' => ['nullable', 'exists:users,id'],
            'type_intervention_ids' => ['nullable', 'array'],
            'type_intervention_ids.*' => ['exists:type_interventions,id'],
            'prochaine_action_date' => ['nullable', 'date'],
            'prochaine_action_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'telephone.regex' => 'Le téléphone doit être un numéro marocain valide (ex: 0612345678).',
            'email.required_without' => 'Renseignez au moins un moyen de contact : téléphone ou e-mail.',
            'telephone.required_without' => 'Renseignez au moins un moyen de contact : téléphone ou e-mail.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom_entreprise' => 'nom',
            'nom_contact' => 'nom du contact',
            'telephone' => 'téléphone',
        ];
    }
}
