<?php

namespace App\Http\Requests;

use App\Models\Formulaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $necessiteChoix = in_array($this->input('type_reponse'), Formulaire::TYPES_AVEC_CHOIX);
        $estTypeNombre  = $this->input('type_reponse') === 'Nombre';
        $estTypeFichierLimitable = in_array($this->input('type_reponse'), ['Photo', 'Document']);

        return [
            'question'          => ['required', 'string', 'max:255'],
            'type_reponse'      => ['required', Rule::in(array_keys(Formulaire::TYPES_CHAMPS))],
            'obligatoire'       => ['boolean'],
            'ordre'             => ['nullable', 'integer', 'min:1'],
            'placeholder'       => ['nullable', 'string', 'max:255'],
            'valeur_par_defaut' => ['nullable', 'string', 'max:1000'],

            // Si le type nécessite des choix, 'choix' doit être un tableau d'au moins 2 éléments
            'choix'             => [
                Rule::requiredIf(fn() => $necessiteChoix),
                'array',
                $necessiteChoix ? 'min:2' : 'nullable',
            ],
            'choix.*.valeur'    => ['required_with:choix', 'string', 'max:255'],
            'choix.*.libelle'   => ['nullable', 'string', 'max:255'],
            'choix.*.ordre'     => ['nullable', 'integer', 'min:1'],

            // Options spécifiques au type "Nombre"
            'nombre_min'        => [$estTypeNombre ? 'nullable' : 'prohibited', 'numeric'],
            'nombre_max'        => [$estTypeNombre ? 'nullable' : 'prohibited', 'numeric', 'gte:nombre_min'],
            'nombre_unite'      => [$estTypeNombre ? 'nullable' : 'prohibited', 'string', 'max:20'],

            // Option spécifique aux types "Photo" et "Document"
            'fichiers_max'      => [$estTypeFichierLimitable ? 'nullable' : 'prohibited', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'choix.required'      => 'Ajoutez au moins deux choix pour ce type de champ.',
            'choix.min'           => 'Ajoutez au moins deux choix pour ce type de champ.',
            'choix.*.valeur.required_with' => 'Chaque choix doit avoir un libellé.',
            'nombre_max.gte'      => 'La valeur maximale doit être supérieure ou égale à la valeur minimale.',
            'nombre_min.prohibited' => 'Le minimum ne s\'applique qu\'au type "Nombre".',
            'nombre_max.prohibited' => 'Le maximum ne s\'applique qu\'au type "Nombre".',
            'nombre_unite.prohibited' => 'L\'unité ne s\'applique qu\'au type "Nombre".',
            'fichiers_max.prohibited' => 'Le nombre maximal de fichiers ne s\'applique qu\'aux types "Photo" et "Document".',
        ];
    }
}
