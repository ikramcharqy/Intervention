<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * "Date d'entrée" n'est modifiable que par un Admin/Super Admin, y compris
     * sur sa propre fiche : un Commercial/Technicien ne doit pas pouvoir la
     * changer via une requête directe même si le champ est désactivé côté vue.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->user()->hasAnyRole(['Super Admin', 'admin'])) {
            $this->merge([
                'date_entree' => $this->user()->date_entree?->format('Y-m-d'),
            ]);
        }

        // Cases à cocher non cochées : absentes du payload HTML, donc explicitées ici
        // en false plutôt que silencieusement ignorées. Seules les clés dont la case
        // est réellement affichée pour ce rôle sont écrasées — les autres gardent leur
        // valeur actuelle (ex: un Admin n'a pas la case "relances_devis" à l'écran).
        $existing = $this->user()->notification_preferences ?? [];
        $isCommercial = $this->user()->hasRole('Commercial');
        $isTechnicienOuAdmin = $this->user()->hasAnyRole(['technicien', 'Technicien', 'Super Admin', 'admin']);

        $preferences = $existing;
        if ($isTechnicienOuAdmin) {
            $preferences['intervention_updates'] = $this->boolean('notification_preferences.intervention_updates');
        }
        if ($isCommercial) {
            $preferences['nouveaux_prospects_assignes'] = $this->boolean('notification_preferences.nouveaux_prospects_assignes');
            $preferences['relances_devis'] = $this->boolean('notification_preferences.relances_devis');
        }

        $this->merge(['notification_preferences' => $preferences]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'prenom'  => ['required', 'string', 'max:255'],
            'email'   => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'adresse' => ['nullable', 'string', 'max:500'],
            'photo'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],

            // Informations professionnelles
            'telephone'         => ['required', 'string', 'max:20', 'regex:/^[0-9\+\s\-\(\)]+$/'],
            'poste'             => ['nullable', 'string', 'max:100'],
            'departement'       => ['nullable', 'string', 'max:100'],
            'zone_geographique' => ['nullable', 'string', 'max:100'],
            'date_entree'       => ['nullable', 'date', 'before_or_equal:today'],

            // Préférences de notification
            'notification_preferences' => ['nullable', 'array'],
            'notification_preferences.intervention_updates' => ['nullable', 'boolean'],
            'notification_preferences.nouveaux_prospects_assignes' => ['nullable', 'boolean'],
            'notification_preferences.relances_devis' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'telephone.required' => 'Le téléphone professionnel est obligatoire.',
            'telephone.regex'    => 'Le numéro de téléphone contient des caractères invalides.',
            'photo.image'        => 'Le fichier doit être une image.',
            'photo.mimes'        => 'L\'image doit être au format jpeg, png, jpg ou webp.',
            'photo.max'          => 'La photo ne doit pas dépasser 5 Mo.',
            'date_entree.before_or_equal' => 'La date d\'entrée ne peut pas être dans le futur.',
        ];
    }
}
