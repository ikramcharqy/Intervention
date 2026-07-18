<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un utilisateur.
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name'      => ['required', 'string', 'max:255'],
            'prenom'    => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'telephone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\+\s\-\(\)]+$/',
            ],
            'adresse'   => ['nullable', 'string', 'max:500'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'password'  => [
                'nullable', // Facultatif lors de la modification
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'roles'     => ['required', 'array'],
            'roles.*'   => ['string', Rule::exists('roles', 'name')],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'prenom.required'    => 'Le prénom est obligatoire.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.email'        => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.regex'    => 'Le numéro de téléphone contient des caractères invalides.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'photo.image'        => 'Le fichier doit être une image.',
            'photo.mimes'        => 'L\'image doit être de format jpeg, png, jpg, gif ou webp.',
            'photo.max'          => 'L\'image ne doit pas dépasser 2 Mo.',
            'roles.required'     => 'Vous devez attribuer au moins un rôle à cet utilisateur.',
            'roles.*.exists'     => 'Le rôle sélectionné n\'existe pas.',
        ];
    }

    /**
     * Noms des attributs traduits en français.
     */
    public function attributes(): array
    {
        return [
            'name'      => 'nom',
            'prenom'    => 'prénom',
            'email'     => 'adresse e-mail',
            'telephone' => 'téléphone',
            'adresse'   => 'adresse',
            'photo'     => 'photo de profil',
            'password'  => 'mot de passe',
            'roles'     => 'rôles',
            'is_active' => 'statut actif',
        ];
    }
}
