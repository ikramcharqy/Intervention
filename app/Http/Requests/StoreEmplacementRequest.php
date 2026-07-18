<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmplacementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chantier_id' => [
                'required',
                'integer',
                Rule::exists('chantiers', 'id')->where('is_active', true),
            ],
            'nom'         => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'nfc_uid'     => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'chantier_id.required' => 'Veuillez sélectionner un chantier.',
            'chantier_id.exists'   => 'Le chantier sélectionné n\'existe pas ou est inactif.',
            'nom.required'         => 'Le nom de l\'emplacement est obligatoire.',
            'latitude.between'     => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between'    => 'La longitude doit être comprise entre -180 et 180.',
        ];
    }

    public function attributes(): array
    {
        return [
            'chantier_id' => 'chantier',
            'nom'         => 'nom de l\'emplacement',
            'description' => 'description',
            'latitude'    => 'latitude',
            'longitude'   => 'longitude',
            'nfc_uid'     => 'UID NFC',
            'is_active'   => 'statut actif',
        ];
    }
}
