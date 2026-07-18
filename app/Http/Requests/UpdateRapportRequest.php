<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRapportRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un rapport.
     */
    public function rules(): array
    {
        $rapport = $this->route('rapport');
        $rapportId = $rapport instanceof \App\Models\Rapport ? $rapport->id : $rapport;

        return [
            'intervention_id' => [
                'required',
                'integer',
                Rule::exists('interventions', 'id'),
                Rule::unique('rapports', 'intervention_id')->ignore($rapportId),
            ],
            'date_debut'           => ['required', 'date'],
            'date_fin'             => ['required', 'date', 'after:date_debut'],
            'commentaire'          => ['nullable', 'string', 'max:5000'],
            'signature_client'     => ['nullable', 'string', 'max:255'],
            'signature_technicien' => ['nullable', 'string', 'max:255'],
            'pdf_path'             => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'intervention_id.required' => 'L\'intervention associée est obligatoire.',
            'intervention_id.exists'   => 'L\'intervention sélectionnée n\'existe pas.',
            'intervention_id.unique'   => 'Cette intervention est déjà associée à un autre rapport.',
            'date_debut.required'      => 'La date et heure de début sont obligatoires.',
            'date_fin.required'        => 'La date et heure de fin sont obligatoires.',
            'date_fin.after'           => 'La date de fin doit être postérieure à la date de début.',
        ];
    }

    /**
     * Noms des attributs traduits en français.
     */
    public function attributes(): array
    {
        return [
            'intervention_id'      => 'intervention',
            'date_debut'           => 'date de début',
            'date_fin'             => 'date de fin',
            'commentaire'          => 'commentaire/observations',
            'signature_client'     => 'signature du client',
            'signature_technicien' => 'signature du technicien',
            'pdf_path'             => 'chemin du fichier PDF',
        ];
    }
}
