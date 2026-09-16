<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionChoixRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'choix'           => ['required', 'array', 'min:2'],
            'choix.*.valeur'  => ['required', 'string', 'max:255'],
            'choix.*.libelle' => ['nullable', 'string', 'max:255'],
            'choix.*.ordre'   => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'choix.required' => 'Ajoutez au moins deux choix pour ce champ.',
            'choix.min'      => 'Ajoutez au moins deux choix pour ce champ.',
        ];
    }
}
