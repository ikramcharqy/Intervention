<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Intervention;

class RemplissageFormulaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $intervention = $this->route('intervention');
        
        // Charger le formulaire associé
        $formulaire = $intervention->typeIntervention?->formulaire;
        
        if (!$formulaire || !$formulaire->is_active) {
            return []; // Pas de règles si pas de formulaire
        }

        $rules = [];

        foreach ($formulaire->questions as $question) {
            $field = 'reponses.' . $question->id;
            $ruleList = [];

            if ($question->obligatoire) {
                $ruleList[] = 'required';
            } else {
                $ruleList[] = 'nullable';
            }

            switch ($question->type_reponse) {
                case 'Nombre':
                    $ruleList[] = 'numeric';
                    break;
                case 'Date':
                    $ruleList[] = 'date';
                    break;
                case 'Heure':
                    $ruleList[] = 'date_format:H:i';
                    break;
                case 'DateHeure':
                    $ruleList[] = 'date'; // ou date_format spécifique
                    break;
                case 'OuiNon':
                    $ruleList[] = 'boolean';
                    break;
                case 'Checkbox':
                    $ruleList[] = 'array';
                    break;
                case 'Photo':
                    $ruleList[] = 'image';
                    $ruleList[] = 'mimes:' . \App\Models\Setting::get('allowed_photo_formats', 'jpg,jpeg,png,webp');
                    $ruleList[] = 'max:5120'; // 5MB max
                    break;
                case 'Signature':
                    // Capture canvas (toujours PNG/JPEG) — pas soumise au réglage
                    // "formats photo autorisés" de la page Paramètres, qui concerne les
                    // photos prises par l'appareil du technicien, pas les signatures.
                    $ruleList[] = 'image';
                    $ruleList[] = 'mimes:jpeg,png,jpg,webp';
                    $ruleList[] = 'max:5120'; // 5MB max
                    break;
                case 'Document':
                    $ruleList[] = 'file';
                    $ruleList[] = 'max:10240'; // 10MB max
                    break;
                case 'Materiaux':
                    $ruleList[] = 'array';
                    $rules[$field . '.*.materiau_id'] = ['required', 'exists:materiaus,id'];
                    $rules[$field . '.*.quantite']    = ['required', 'numeric', 'min:0.01'];
                    $rules[$field . '.*.commentaire']  = ['nullable', 'string', 'max:500'];
                    break;
                default:
                    // Texte, TexteLong, Liste, Radio, GPS, QRCode
                    if ($question->type_reponse !== 'Checkbox') {
                        $ruleList[] = 'string';
                    }
                    break;
            }

            $rules[$field] = $ruleList;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'reponses.*.required' => 'Ce champ est obligatoire.',
            'reponses.*.numeric'  => 'Veuillez entrer un nombre valide.',
            'reponses.*.image'    => 'Le fichier doit être une image.',
            'reponses.*.file'     => 'Veuillez uploader un fichier valide.',
            'reponses.*.date'     => 'Veuillez entrer une date valide.',
        ];
    }
}
