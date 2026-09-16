<?php

namespace App\Http\Requests;

use App\Models\ClientParticulier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Les champs entreprise (ICE, IF, RC, Patente) n'ont de sens que pour un client
     * de type Entreprise, et les champs d'identité (CIN, date de naissance) que pour
     * un client Particulier. On les nullifie ici avant validation pour empêcher toute
     * incohérence forcée (ex: appel API direct envoyant ICE avec type_client=Particulier).
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('type_client') !== 'Entreprise') {
            $this->merge([
                'ice' => null,
                'if' => null,
                'rc' => null,
                'patente' => null,
            ]);
        }

        if ($this->input('type_client') !== 'Particulier') {
            $this->merge([
                'numero_cin' => null,
                'date_naissance' => null,
            ]);
        }
    }

    public function rules(): array
    {
        $client = $this->route('client');

        return [
            'code_client' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('clients', 'code_client')->ignore($client),
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
                Rule::unique('clients', 'email')->ignore($client),
            ],
            'adresse_facturation' => ['nullable', 'string', 'max:500'],
            'ville'               => ['required', 'string', 'max:100'],
            'pays'                => ['nullable', 'string', 'max:100'],
            'observations'        => ['nullable', 'string', 'max:1000'],
            'is_active'           => ['boolean'],
            'commercial_id'       => ['nullable', 'exists:users,id'],

            // Données entreprise (colonnes conformes aux migrations) : ICE obligatoire
            // uniquement pour un client Entreprise (identifiant légalement requis au Maroc).
            'ice'     => [
                Rule::requiredIf(fn () => $this->input('type_client') === 'Entreprise'),
                'nullable', 'string', 'max:15',
                Rule::unique('client_entreprises', 'ice')->ignore($client?->clientEntreprise?->id),
            ],
            'if'      => ['nullable', 'string', 'max:20'],
            'rc'      => ['nullable', 'string', 'max:20'],
            'patente' => ['nullable', 'string', 'max:20'],

            // Identité du client Particulier (loi 09-08) : CIN obligatoire, format
            // souple (1-2 lettres + chiffres, variable selon la préfecture d'émission).
            'numero_cin' => [
                Rule::requiredIf(fn () => $this->input('type_client') === 'Particulier'),
                'nullable', 'string', 'max:20',
                'regex:/^[A-Za-z]{1,2}[0-9]{1,8}$/',
                function ($attribute, $value, $fail) use ($client) {
                    if (!$value) {
                        return;
                    }
                    $query = ClientParticulier::where('numero_cin_hash', ClientParticulier::hashCin($value));
                    if ($client?->clientParticulier) {
                        $query->where('id', '!=', $client->clientParticulier->id);
                    }
                    if ($query->exists()) {
                        $fail('Ce numéro CIN est déjà enregistré pour un autre client.');
                    }
                },
            ],
            'date_naissance' => ['nullable', 'date', 'before:today'],
            'cin_recto' => ['nullable', 'prohibited_unless:type_client,Particulier', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'cin_verso' => ['nullable', 'prohibited_unless:type_client,Particulier', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'code_client.required'  => 'Le code client est obligatoire.',
            'code_client.unique'    => 'Ce code client est déjà utilisé par un autre client.',
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
            'ice.required'          => 'L\'ICE est obligatoire pour un client de type Entreprise.',
            'ice.unique'            => 'Cet ICE est déjà enregistré pour un autre client.',
            'numero_cin.required'   => 'Le numéro CIN est obligatoire pour un client Particulier.',
            'numero_cin.regex'      => 'Le format du numéro CIN est invalide (ex: AB123456).',
            'date_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'cin_recto.mimes'       => 'Le recto de la CIN doit être un PDF, JPG ou PNG.',
            'cin_verso.mimes'       => 'Le verso de la CIN doit être un PDF, JPG ou PNG.',
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
            'numero_cin'           => 'numéro CIN',
            'date_naissance'       => 'date de naissance',
            'cin_recto'            => 'scan CIN recto',
            'cin_verso'            => 'scan CIN verso',
        ];
    }
}
