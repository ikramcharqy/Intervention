<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference'    => ['nullable', 'string', 'max:100'],
            'nom'          => ['required', 'string', 'max:255'],
            'unite'        => ['required', 'string', 'max:50'],
            'prix_unitaire'=> ['nullable', 'numeric', 'min:0'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'is_active'    => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'   => 'Le nom du matériau est obligatoire.',
            'unite.required' => 'L\'unité de mesure (ex: kg, pièce) est obligatoire.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom'         => 'nom du matériau',
            'unite'       => 'unité de mesure',
            'description' => 'description',
            'is_active'   => 'statut actif',
        ];
    }
}
