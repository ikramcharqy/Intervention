<?php

namespace App\Http\Requests;

use App\Models\Materiau;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMateriauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $materiauId = $this->route('materiau')?->id;

        return [
            'reference'        => ['required', 'string', 'max:100', Rule::unique('materiaus', 'reference')->ignore($materiauId)],
            'nom'              => ['required', 'string', 'max:255'],
            'categorie'        => ['required', Rule::in(Materiau::CATEGORIES)],
            'marque'           => ['nullable', 'string', 'max:255'],
            'modele_fabricant' => ['nullable', 'string', 'max:255'],
            'unite'            => ['required', 'string', 'max:50'],
            'prix_unitaire'    => ['required', 'numeric', 'min:0.01'],
            'seuil_alerte'     => ['nullable', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'image'            => ['nullable', 'image', 'max:4096'],
            'fiche_technique'  => ['nullable', 'file', 'mimes:pdf', 'max:8192'],
            'is_active'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'           => 'Le nom du matériau est obligatoire.',
            'unite.required'         => 'L\'unité de mesure est obligatoire.',
            'categorie.required'     => 'La catégorie est obligatoire.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'reference.required'     => 'La référence est obligatoire.',
            'reference.unique'       => 'Cette référence est déjà utilisée par un autre matériau.',
            'fiche_technique.mimes'  => 'La fiche technique doit être un fichier PDF.',
            'fiche_technique.max'    => 'La fiche technique ne doit pas dépasser 8 Mo.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nom'              => 'nom du matériau',
            'unite'            => 'unité de mesure',
            'description'      => 'description',
            'categorie'        => 'catégorie',
            'marque'           => 'marque',
            'modele_fabricant' => 'modèle fabricant',
            'prix_unitaire'    => 'prix unitaire',
            'seuil_alerte'     => 'seuil d\'alerte',
            'is_active'        => 'statut actif',
            'fiche_technique'  => 'fiche technique',
        ];
    }
}
