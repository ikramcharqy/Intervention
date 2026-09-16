<?php

namespace App\Http\Requests;

use App\Models\Chantier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChantierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chantier = $this->route('chantier');

        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->where('is_active', true),
            ],
            'code_chantier' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('chantiers', 'code_chantier')->ignore($chantier),
            ],
            'nom'        => ['required', 'string', 'max:255'],
            'type_local' => [
                'required',
                Rule::in(Chantier::TYPES_LOCAL),
            ],
            'adresse'   => ['required', 'string', 'max:500'],
            'ville'     => ['required', 'string', 'max:100'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'responsable'           => ['required', 'string', 'max:255'],
            'telephone_responsable' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\s\-\(\)]+$/',
            ],
            'email_responsable' => ['required', 'email:rfc,dns', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'is_active'         => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists'   => 'Le client sélectionné n\'existe pas ou est inactif.',
            'code_chantier.required' => 'Le code chantier est obligatoire.',
            'code_chantier.unique'   => 'Ce code chantier est déjà utilisé par un autre chantier.',
            'code_chantier.regex'    => 'Le code chantier ne peut contenir que des lettres majuscules, des chiffres et des tirets.',
            'nom.required'        => 'Le nom du chantier est obligatoire.',
            'type_local.required' => 'Le type de local est obligatoire.',
            'type_local.in'       => 'Le type de local sélectionné n\'est pas valide.',
            'adresse.required'   => 'L\'adresse est obligatoire.',
            'ville.required'     => 'La ville est obligatoire.',
            'latitude.between'   => 'La latitude doit être comprise entre -90 et 90.',
            'longitude.between'  => 'La longitude doit être comprise entre -180 et 180.',
            'responsable.required'           => 'Le nom du responsable est obligatoire.',
            'telephone_responsable.required' => 'Le téléphone du responsable est obligatoire.',
            'telephone_responsable.regex'    => 'Le téléphone du responsable contient des caractères invalides.',
            'email_responsable.required'     => 'L\'e-mail du responsable est obligatoire.',
            'email_responsable.email'        => 'L\'e-mail du responsable n\'est pas valide.',
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id'             => 'client',
            'code_chantier'         => 'code chantier',
            'nom'                   => 'nom du chantier',
            'type_local'            => 'type de local',
            'adresse'               => 'adresse',
            'ville'                 => 'ville',
            'latitude'              => 'latitude',
            'longitude'             => 'longitude',
            'responsable'           => 'responsable',
            'telephone_responsable' => 'téléphone du responsable',
            'email_responsable'     => 'e-mail du responsable',
            'description'           => 'description',
            'is_active'             => 'statut actif',
        ];
    }
}
