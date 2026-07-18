<?php

namespace App\Http\Requests;

use App\Models\Formulaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'question'          => ['required', 'string', 'max:255'],
            'type_reponse'      => ['required', Rule::in(array_keys(Formulaire::TYPES_CHAMPS))],
            'obligatoire'       => ['boolean'],
            'ordre'             => ['nullable', 'integer', 'min:1'],
            'placeholder'       => ['nullable', 'string', 'max:255'],
            'valeur_par_defaut' => ['nullable', 'string', 'max:1000'],
            
            // Si le type nécessite des choix, 'choix' doit être un tableau
            'choix'             => [
                Rule::requiredIf(fn() => in_array($this->input('type_reponse'), Formulaire::TYPES_AVEC_CHOIX)),
                'array'
            ],
            'choix.*.valeur'    => ['required_with:choix', 'string', 'max:255'],
            'choix.*.libelle'   => ['nullable', 'string', 'max:255'],
            'choix.*.ordre'     => ['nullable', 'integer', 'min:1'],
        ];
    }
}
