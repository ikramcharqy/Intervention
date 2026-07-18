<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTacheRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une tâche.
     */
    public function rules(): array
    {
        $tache = $this->route('tache');
        $tacheId = $tache instanceof \App\Models\Tache ? $tache->id : $tache;

        return [
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taches', 'nom')->ignore($tacheId),
            ],
            'description'    => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('taches', 'id'),
                Rule::notIn([$tacheId]), // Une tâche ne peut pas être son propre parent
            ],
            'ordre'          => ['required', 'integer', 'min:1'],
            'duree_estimee'  => ['nullable', 'integer', 'min:1', 'max:1440'],
            'is_obligatoire' => ['boolean'],
            'is_active'      => ['boolean'],
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'nom.required'      => 'Le nom de la tâche est obligatoire.',
            'nom.unique'        => 'Ce nom de tâche est déjà utilisé par une autre tâche.',
            'nom.max'           => 'Le nom ne doit pas dépasser :max caractères.',
            'parent_id.exists'  => 'La tâche parente sélectionnée n\'existe pas.',
            'parent_id.not_in'  => 'Une tâche ne peut pas être sa propre tâche parente.',
            'ordre.required'    => 'L\'ordre d\'affichage est obligatoire.',
            'ordre.integer'     => 'L\'ordre d\'affichage doit être un nombre entier.',
            'ordre.min'         => 'L\'ordre d\'affichage doit être supérieur ou égal à 1.',
            'duree_estimee.min' => 'La durée estimée doit être d\'au moins :min minute.',
            'duree_estimee.max' => 'La durée estimée ne peut pas dépasser 24h (:max minutes).',
        ];
    }

    /**
     * Noms des attributs traduits en français.
     */
    public function attributes(): array
    {
        return [
            'nom'            => 'nom de la tâche',
            'description'    => 'description',
            'parent_id'      => 'tâche parente',
            'ordre'          => 'ordre d\'affichage',
            'duree_estimee'  => 'durée estimée',
            'is_obligatoire' => 'obligatoire',
            'is_active'      => 'statut actif',
        ];
    }
}
