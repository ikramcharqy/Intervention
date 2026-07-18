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
                case 'Signature':
                    $ruleList[] = 'image';
                    $ruleList[] = 'max:5120'; // 5MB max
                    break;
                case 'Document':
                    $ruleList[] = 'file';
                    $ruleList[] = 'max:10240'; // 10MB max
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
