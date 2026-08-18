<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\Reponse;
use App\Models\Question;
use App\Models\Formulaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class RemplissageFormulaireService
{
    /**
     * Sauvegarde les réponses au formulaire dynamique pour une intervention donnée.
     */
    public function sauvegarderReponses(Intervention $intervention, Formulaire $formulaire, array $data, $files = []): void
    {
        DB::transaction(function () use ($intervention, $formulaire, $data, $files) {
            // 1. Récupérer ou créer un rapport pour associer les réponses
            $rapport = Rapport::firstOrCreate(
                ['intervention_id' => $intervention->id],
                [
                    'date_debut' => now(),
                    'date_fin'   => now(),
                    'commentaire' => 'Formulaire rempli',
                ]
            );

            // 2. Nettoyer les anciennes réponses du formulaire pour ce rapport pour éviter les doublons en cas d'édition
            Reponse::where('rapport_id', $rapport->id)->delete();

            // 3. Parcourir les questions et stocker les réponses
            foreach ($formulaire->questions as $question) {
                // Fichiers uploadés
                if (isset($files[$question->id])) {
                    $file = $files[$question->id];
                    $path = null;
                    if (is_array($file)) {
                        $paths = [];
                        foreach ($file as $f) {
                            if ($f instanceof UploadedFile) {
                                $paths[] = $f->store('formulaires_fichiers', 'public');
                            }
                        }
                        $path = implode(', ', $paths);
                    } elseif ($file instanceof UploadedFile) {
                        $path = $file->store('formulaires_fichiers', 'public');
                    }

                    if ($path) {
                        Reponse::create([
                            'rapport_id'      => $rapport->id,
                            'question_id'     => $question->id,
                            'reponse_fichier' => $path,
                        ]);
                    }
                    continue;
                }

                // Matériaux utilisés (gérés séparément dans l'onglet Matériaux)
                if ($question->type_reponse === 'Materiaux') {
                    continue;
                }

                // Si pas de réponse dans les données, on skip
                if (!isset($data[$question->id])) {
                    continue;
                }

                $reponseValue = $data[$question->id];

                if ($reponseValue === null || $reponseValue === '') {
                    continue;
                }

                // Checkbox
                if ($question->type_reponse === 'Checkbox') {
                    if (is_array($reponseValue)) {
                        if (count($reponseValue) === 1 && is_numeric(reset($reponseValue))) {
                            Reponse::create([
                                'rapport_id'        => $rapport->id,
                                'question_id'       => $question->id,
                                'choix_question_id' => (int)reset($reponseValue),
                            ]);
                        } else {
                            Reponse::create([
                                'rapport_id'    => $rapport->id,
                                'question_id'   => $question->id,
                                'reponse_texte' => $this->formatStringValue($reponseValue),
                            ]);
                        }
                    } else {
                        if (is_numeric($reponseValue)) {
                            Reponse::create([
                                'rapport_id'        => $rapport->id,
                                'question_id'       => $question->id,
                                'choix_question_id' => (int)$reponseValue,
                            ]);
                        } else {
                            Reponse::create([
                                'rapport_id'    => $rapport->id,
                                'question_id'   => $question->id,
                                'reponse_texte' => $this->formatStringValue($reponseValue),
                            ]);
                        }
                    }
                    continue;
                }

                // Liste / Radio
                if (in_array($question->type_reponse, ['Liste', 'Radio'])) {
                    $singleVal = is_array($reponseValue) ? reset($reponseValue) : $reponseValue;
                    if (is_numeric($singleVal)) {
                        Reponse::create([
                            'rapport_id'        => $rapport->id,
                            'question_id'       => $question->id,
                            'choix_question_id' => (int)$singleVal,
                        ]);
                    } else {
                        Reponse::create([
                            'rapport_id'    => $rapport->id,
                            'question_id'   => $question->id,
                            'reponse_texte' => $this->formatStringValue($singleVal),
                        ]);
                    }
                    continue;
                }

                // Nombre
                if ($question->type_reponse === 'Nombre') {
                    $numVal = is_array($reponseValue) ? reset($reponseValue) : $reponseValue;
                    if ($numVal !== null && $numVal !== '' && is_numeric($numVal)) {
                        Reponse::create([
                            'rapport_id'     => $rapport->id,
                            'question_id'    => $question->id,
                            'reponse_nombre' => $numVal,
                        ]);
                    } elseif ($numVal !== null && $numVal !== '') {
                        Reponse::create([
                            'rapport_id'    => $rapport->id,
                            'question_id'   => $question->id,
                            'reponse_texte' => $this->formatStringValue($numVal),
                        ]);
                    }
                    continue;
                }

                // Oui/Non
                if (in_array($question->type_reponse, ['OuiNon', 'Oui_Non'])) {
                    $boolVal = is_array($reponseValue) ? reset($reponseValue) : $reponseValue;
                    Reponse::create([
                        'rapport_id'    => $rapport->id,
                        'question_id'   => $question->id,
                        'reponse_texte' => $this->formatStringValue($boolVal),
                    ]);
                    continue;
                }

                // Types texte, fichiers/médias, fallback
                Reponse::create([
                    'rapport_id'    => $rapport->id,
                    'question_id'   => $question->id,
                    'reponse_texte' => $this->formatStringValue($reponseValue),
                ]);
            }
        });
    }

    /**
     * Formate n'importe quelle valeur (tableau, booléen, string) de manière sécurisée pour reponse_texte.
     */
    private function formatStringValue($val): ?string
    {
        if (is_null($val)) {
            return null;
        }
        if (is_array($val)) {
            $formatted = array_map(function ($item) {
                return is_array($item) ? json_encode($item, JSON_UNESCAPED_UNICODE) : (string)$item;
            }, $val);
            return implode(', ', array_filter($formatted, fn($item) => $item !== ''));
        }
        if (is_bool($val)) {
            return $val ? 'Oui' : 'Non';
        }
        return (string)$val;
    }
}
