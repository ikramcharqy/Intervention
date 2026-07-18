<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTypeInterventionRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un type d'intervention.
     * La règle unique ignore l'enregistrement courant.
     */
    public function rules(): array
    {
        $typeIntervention = $this->route('type_intervention');

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('type_interventions', 'nom')->ignore($typeIntervention),
            ],
            'description'   => ['nullable', 'string', 'max:1000'],
            'duree_estimee' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'is_active'     => ['boolean'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'nom.required'          => 'Le nom du type d\'intervention est obligatoire.',
            'nom.unique'            => 'Ce nom de type d\'intervention est déjà utilisé.',
            'nom.max'               => 'Le nom ne doit pas dépasser :max caractères.',
            'duree_estimee.min'     => 'La durée estimée doit être d\'au moins :min minute.',
            'duree_estimee.max'     => 'La durée estimée ne doit pas dépasser :max minutes.',
            'duree_estimee.integer' => 'La durée estimée doit être un nombre entier de minutes.',
        ];
    }

    /**
     * Noms des attributs traduits en français.
     */
    public function attributes(): array
    {
        return [
            'nom'           => 'nom du type d\'intervention',
            'description'   => 'description',
            'duree_estimee' => 'durée estimée (minutes)',
            'is_active'     => 'statut actif',
        ];
    }
}
