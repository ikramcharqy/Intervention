<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Étape 1/2/3 du prompt "Créer un Nouveau Rôle" : bloque la duplication d'un rôle
 * système (insensible à la casse/espaces), contraint le format du nom (cohérent avec
 * `hasRole()` Spatie, qui compare la chaîne telle quelle), et valide le champ optionnel
 * de clonage de permissions.
 */
class StoreRoleRequest extends FormRequest
{
    /**
     * Rôles système actuels — recréer l'un de ces noms (même avec une casse ou des
     * espaces différents) créerait un doublon ambigu prêtant à confusion avec le rôle
     * réel, sans jamais s'y substituer fonctionnellement (Spatie compare les noms tels
     * quels) : source de failure silencieuse en autorisation.
     */
    public const RESERVED_ROLE_NAMES = ['super admin', 'admin', 'commercial', 'technicien', 'client'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Format cohérent avec l'usage Spatie (hasRole('Nom Exact') compare la
            // chaîne littéralement) : lettres (avec accents), chiffres, espaces et
            // tirets uniquement, pour éviter qu'un caractère spécial ne casse une
            // comparaison ou un attribut HTML ailleurs (ex: badges, sélecteurs).
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\pL\pN \-]+$/u',
                // Vérifié avant Rule::unique() ci-dessous : un nom réservé doit toujours
                // afficher le message explicite "rôle système", pas le message générique
                // d'unicité (les deux règles échouent ensemble sur ces noms).
                function (string $attribute, $value, \Closure $fail) {
                    if (in_array(strtolower(trim($value)), self::RESERVED_ROLE_NAMES, true)) {
                        $fail('Ce nom de rôle est déjà utilisé par un rôle système.');
                    }
                },
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'clone_from' => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Le nom du rôle ne peut contenir que des lettres, chiffres, espaces et tirets.',
            'name.unique' => 'Ce nom de rôle existe déjà.',
        ];
    }

    /**
     * Rule::unique() ci-dessus ne couvre que l'égalité stricte — la casse/les espaces
     * en trop ("super admin", "Super Admin ") passeraient outre. Vérification
     * insensible à la casse en complément, sur le nom déjà trim() par Laravel.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $name = trim((string) $this->input('name'));

            if ($name === '') {
                return;
            }

            $existsCaseInsensitive = \Spatie\Permission\Models\Role::where('guard_name', 'web')
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->exists();

            if ($existsCaseInsensitive && ! $validator->errors()->has('name')) {
                $validator->errors()->add('name', 'Ce nom de rôle existe déjà (à la casse ou aux espaces près).');
            }
        });
    }
}
