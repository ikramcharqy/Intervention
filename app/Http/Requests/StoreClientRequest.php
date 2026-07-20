<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_client' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('clients', 'code_client'),
            ],
            'type_client' => [
                'required',
                Rule::in(['Entreprise', 'Particulier', 'Administration']),
            ],
            'nom'         => ['required', 'string', 'max:255'],
            'nom_contact' => ['nullable', 'string', 'max:255'],
            'telephone'   => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\s\-\(\)]+$/',
            ],
            'telephone_secondaire' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9\+\s\-\(\)]+$/',
            ],
            'email' => [
                'nullable',
                'email:rfc,dns',
                'max:255',
                Rule::unique('clients', 'email'),
            ],
            'adresse_facturation' => ['nullable', 'string', 'max:500'],
            'ville'               => ['required', 'string', 'max:100'],
            'pays'                => ['nullable', 'string', 'max:100'],
            'observations'        => ['nullable', 'string', 'max:1000'],
            'is_active'           => ['boolean'],
            'commercial_id'       => ['nullable', 'exists:users,id'],

            // Données entreprise (colonnes conformes aux migrations)
            'ice'     => ['nullable', 'string', 'max:15'],
            'if'      => ['nullable', 'string', 'max:20'],
            'rc'      => ['nullable', 'string', 'max:20'],
            'patente' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'code_client.required'  => 'Le code client est obligatoire.',
            'code_client.unique'    => 'Ce code client existe déjà.',
            'code_client.regex'     => 'Le code client ne peut contenir que des lettres majuscules, des chiffres et des tirets.',
            'code_client.max'       => 'Le code client ne doit pas dépasser :max caractères.',
            'type_client.required'  => 'Le type de client est obligatoire.',
            'type_client.in'        => 'Le type de client doit être : Entreprise, Particulier ou Administration.',
            'nom.required'          => 'Le nom du client est obligatoire.',
            'nom.max'               => 'Le nom ne doit pas dépasser :max caractères.',
            'telephone.required'    => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex'       => 'Le numéro de téléphone contient des caractères invalides.',
            'telephone_secondaire.regex' => 'Le numéro de téléphone secondaire contient des caractères invalides.',
            'email.email'           => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'          => 'Cette adresse e-mail est déjà utilisée par un autre client.',
            'ville.required'        => 'La ville est obligatoire.',
        ];
    }

    public function attributes(): array
    {
        return [
            'code_client'          => 'code client',
            'type_client'          => 'type de client',
            'nom'                  => 'nom',
            'nom_contact'          => 'nom du contact',
            'telephone'            => 'téléphone',
            'telephone_secondaire' => 'téléphone secondaire',
            'email'                => 'adresse e-mail',
            'adresse_facturation'  => 'adresse de facturation',
            'ville'                => 'ville',
            'pays'                 => 'pays',
            'observations'         => 'observations',
            'is_active'            => 'statut actif',
            'ice'                  => 'ICE',
            'if'                   => 'identifiant fiscal (IF)',
            'rc'                   => 'registre de commerce',
            'patente'              => 'patente',
        ];
    }
}
