<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGpsSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Étape 5.1 : bornes resserrées (10-300s) — évite une saisie extrême non
            // intentionnelle (trop bas = décharge batterie, trop haut = suivi imprécis).
            'gps_interval'          => ['required', 'integer', 'min:10', 'max:300'],
            'auto_validate_gps'     => ['nullable', 'boolean'],
            // Étape 4 : rayon de validation de présence — requis dès que la validation
            // automatique est activée.
            'gps_validation_radius' => ['required_if:auto_validate_gps,1', 'nullable', 'integer', 'min:20', 'max:500'],
            'email_notifications'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'gps_interval.min' => 'L\'intervalle GPS doit être d\'au moins :min secondes.',
            'gps_interval.max' => 'L\'intervalle GPS ne doit pas dépasser :max secondes.',
            'gps_validation_radius.required_if' => 'Le rayon de validation est requis lorsque la validation automatique de présence est activée.',
            'gps_validation_radius.min' => 'Le rayon de validation doit être d\'au moins :min mètres.',
            'gps_validation_radius.max' => 'Le rayon de validation ne doit pas dépasser :max mètres.',
        ];
    }
}
