<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDevisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prospect_id' => 'nullable|exists:prospects,id',
            'client_id' => 'nullable|exists:clients,id',
            'demande_intervention_id' => 'nullable|exists:demande_interventions,id',
            'statut' => 'required|in:Brouillon,Envoyé,Accepté,Refusé',
            'date_emission' => 'required|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_emission',
            'taux_tva' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ];
    }
}
