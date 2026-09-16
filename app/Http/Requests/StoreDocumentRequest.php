<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Un document ne peut être lié qu'à une seule entité à la fois (les 4 champs
     * sont mutuellement exclusifs). Chaque entité doit appartenir au commercial
     * connecté : mêmes garanties de confidentialité que le reste du projet
     * (Portefeuille Clients, Prospects, Devis).
     */
    public function rules(): array
    {
        $commercialId = auth()->id();
        $chantierClientIds = Client::where('commercial_id', $commercialId)->pluck('id');

        return [
            'file' => ['required', 'file', 'mimes:pdf,docx,xlsx,jpg,jpeg,png', 'max:10240'],
            'type_document' => ['required', Rule::in(Document::TYPES)],

            'prospect_id' => [
                'nullable',
                'prohibits:client_id,devis_id,chantier_id',
                Rule::exists('prospects', 'id')->where(fn ($q) => $q->where('commercial_id', $commercialId)),
            ],
            'client_id' => [
                'nullable',
                'prohibits:prospect_id,devis_id,chantier_id',
                Rule::exists('clients', 'id')->where(fn ($q) => $q->where('commercial_id', $commercialId)),
            ],
            'devis_id' => [
                'nullable',
                'prohibits:prospect_id,client_id,chantier_id',
                Rule::exists('devis', 'id')->where(fn ($q) => $q->where('commercial_id', $commercialId)),
            ],
            'chantier_id' => [
                'nullable',
                'prohibits:prospect_id,client_id,devis_id',
                Rule::exists('chantiers', 'id')->where(fn ($q) => $q->whereIn('client_id', $chantierClientIds)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Le fichier est obligatoire.',
            'file.mimes' => 'Format non autorisé. Formats acceptés : PDF, DOCX, XLSX, JPG, PNG.',
            'file.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'type_document.required' => 'Le type de document est obligatoire.',
            'type_document.in' => 'Type de document invalide.',
            'prospect_id.exists' => 'Ce prospect ne vous est pas accessible.',
            'client_id.exists' => 'Ce client ne vous est pas accessible.',
            'devis_id.exists' => 'Ce devis ne vous est pas accessible.',
            'chantier_id.exists' => 'Ce chantier ne vous est pas accessible.',
            'prospect_id.prohibits' => 'Un document ne peut être lié qu\'à une seule entité.',
            'client_id.prohibits' => 'Un document ne peut être lié qu\'à une seule entité.',
            'devis_id.prohibits' => 'Un document ne peut être lié qu\'à une seule entité.',
            'chantier_id.prohibits' => 'Un document ne peut être lié qu\'à une seule entité.',
        ];
    }
}
