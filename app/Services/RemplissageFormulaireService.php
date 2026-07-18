<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\Rapport;
use App\Models\Reponse;
use App\Models\Question;
use App\Models\Formulaire;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                    if ($file instanceof UploadedFile) {
                        $path = $file->store('formulaires_fichiers', 'public');
                        
                        Reponse::create([
                            'rapport_id'      => $rapport->id,
                            'question_id'     => $question->id,
                            'reponse_fichier' => $path,
                        ]);
                    }
                    continue;
                }

                // Si pas de réponse dans les données, on skip
                if (!isset($data[$question->id]) && $question->type_reponse !== 'Checkbox') {
                    continue;
                }

                $reponseValue = $data[$question->id] ?? null;

                // Gestion spécifique pour les Checkboxes (multiples réponses)
                if ($question->type_reponse === 'Checkbox') {
                    if (is_array($reponseValue)) {
                        foreach ($reponseValue as $choixId) {
                            Reponse::create([
                                'rapport_id'        => $rapport->id,
                                'question_id'       => $question->id,
                                'choix_question_id' => $choixId,
                            ]);
                        }
                    }
                    continue;
                }

                // Pour les types Liste et Radio (un seul choix stocké sous forme d'ID de choix)
                if (in_array($question->type_reponse, ['Liste', 'Radio'])) {
                    Reponse::create([
                        'rapport_id'        => $rapport->id,
                        'question_id'       => $question->id,
                        'choix_question_id' => $reponseValue,
                    ]);
                    continue;
                }

                // Types texte
                $texteTypes = ['Texte', 'TexteLong', 'Date', 'Heure', 'DateHeure', 'GPS', 'QRCode'];
                if (in_array($question->type_reponse, $texteTypes)) {
                    Reponse::create([
                        'rapport_id'    => $rapport->id,
                        'question_id'   => $question->id,
                        'reponse_texte' => $reponseValue,
                    ]);
                    continue;
                }

                // Nombres
                if ($question->type_reponse === 'Nombre') {
                    Reponse::create([
                        'rapport_id'     => $rapport->id,
                        'question_id'    => $question->id,
                        'reponse_nombre' => $reponseValue,
                    ]);
                    continue;
                }
                
                // Oui/Non (booléen stocké en texte "1" ou "0")
                if ($question->type_reponse === 'OuiNon') {
                    Reponse::create([
                        'rapport_id'    => $rapport->id,
                        'question_id'   => $question->id,
                        'reponse_texte' => $reponseValue ? '1' : '0',
                    ]);
                    continue;
                }
            }
            
            // On peut éventuellement changer le statut de l'intervention à 'Terminee' ou 'Suspendue'
            // Mais la consigne demande juste de sauvegarder les réponses.
        });
    }
}
