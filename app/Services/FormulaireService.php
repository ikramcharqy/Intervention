<?php

namespace App\Services;

use App\Models\Formulaire;
use App\Models\Question;
use App\Models\ChoixQuestion;
use Illuminate\Support\Facades\DB;

class FormulaireService
{
    /*
    |--------------------------------------------------------------------------
    | Formulaires
    |--------------------------------------------------------------------------
    */

    /**
     * Crée un nouveau formulaire avec ses questions initiales (si fournies).
     */
    public function createFormulaire(array $data): Formulaire
    {
        return DB::transaction(function () use ($data) {
            // Un protocole naît toujours inactif : il n'a encore aucune question,
            // il ne peut donc pas être envoyé à un technicien sur le terrain.
            $formulaire = Formulaire::create([
                'type_intervention_id' => $data['type_intervention_id'],
                'nom'                  => $data['nom'],
                'description'          => $data['description'] ?? null,
                'is_active'            => false,
            ]);

            return $formulaire;
        });
    }

    /**
     * Met à jour un formulaire existant.
     *
     * @throws \RuntimeException si on tente d'activer un protocole sans aucune question.
     */
    public function updateFormulaire(Formulaire $formulaire, array $data): Formulaire
    {
        $veutActiver = (bool) ($data['is_active'] ?? $formulaire->is_active);

        if ($veutActiver && $formulaire->questions()->count() === 0) {
            throw new \RuntimeException(
                "Ce protocole ne peut pas être activé : ajoutez au moins une question avant de l'envoyer aux techniciens."
            );
        }

        $formulaire->update([
            'type_intervention_id' => $data['type_intervention_id'],
            'nom'                  => $data['nom'],
            'description'          => $data['description'] ?? null,
            'is_active'            => $veutActiver,
        ]);

        return $formulaire;
    }

    /**
     * Supprime un formulaire et en cascade toutes ses questions et choix.
     */
    public function deleteFormulaire(Formulaire $formulaire): void
    {
        DB::transaction(function () use ($formulaire) {
            $formulaire->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Questions
    |--------------------------------------------------------------------------
    */

    /**
     * Ajoute une question à un formulaire.
     * Calcule automatiquement l'ordre (dernier + 1).
     */
    public function addQuestion(Formulaire $formulaire, array $data): Question
    {
        return DB::transaction(function () use ($formulaire, $data) {
            $ordre = $formulaire->questions()->max('ordre') + 1;

            $question = Question::create([
                'formulaire_id'      => $formulaire->id,
                'question'           => $data['question'],
                'type_reponse'       => $data['type_reponse'],
                'obligatoire'        => $data['obligatoire'] ?? false,
                'ordre'              => $data['ordre'] ?? $ordre,
                'placeholder'        => $data['placeholder'] ?? null,
                'valeur_par_defaut'  => $data['valeur_par_defaut'] ?? null,
                'condition_affichage' => $data['condition_affichage'] ?? null,
                'nombre_min'         => $data['nombre_min'] ?? null,
                'nombre_max'         => $data['nombre_max'] ?? null,
                'nombre_unite'       => $data['nombre_unite'] ?? null,
                'fichiers_max'       => $data['fichiers_max'] ?? null,
            ]);

            // Création des choix si le type en nécessite
            if (in_array($question->type_reponse, Formulaire::TYPES_AVEC_CHOIX)) {
                $this->syncChoix($question, $data['choix'] ?? []);
            }

            return $question;
        });
    }

    /**
     * Met à jour une question et resynchronise ses choix.
     */
    public function updateQuestion(Question $question, array $data): Question
    {
        return DB::transaction(function () use ($question, $data) {
            $question->update([
                'question'          => $data['question'],
                'type_reponse'      => $data['type_reponse'],
                'obligatoire'       => $data['obligatoire'] ?? false,
                'ordre'             => $data['ordre'] ?? $question->ordre,
                'placeholder'       => $data['placeholder'] ?? null,
                'valeur_par_defaut' => $data['valeur_par_defaut'] ?? null,
                'nombre_min'        => $data['nombre_min'] ?? null,
                'nombre_max'        => $data['nombre_max'] ?? null,
                'nombre_unite'      => $data['nombre_unite'] ?? null,
                'fichiers_max'      => $data['fichiers_max'] ?? null,
            ]);

            // Resynchronisation des choix
            if (in_array($question->type_reponse, Formulaire::TYPES_AVEC_CHOIX)) {
                $this->syncChoix($question, $data['choix'] ?? []);
            } else {
                // Si le type ne supporte plus de choix, on les supprime
                $question->choix()->delete();
            }

            return $question->fresh();
        });
    }

    /**
     * Supprime une question et ses choix.
     */
    public function deleteQuestion(Question $question): void
    {
        DB::transaction(function () use ($question) {
            $question->choix()->delete();
            $question->delete();
        });
    }

    /**
     * Réordonne les questions d'un formulaire.
     * $ordre = [question_id => nouvel_ordre, ...]
     */
    public function reorderQuestions(Formulaire $formulaire, array $ordre): void
    {
        DB::transaction(function () use ($formulaire, $ordre) {
            foreach ($ordre as $questionId => $newOrdre) {
                Question::where('id', $questionId)
                    ->where('formulaire_id', $formulaire->id)
                    ->update(['ordre' => $newOrdre]);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Choix
    |--------------------------------------------------------------------------
    */

    /**
     * Synchronise les choix d'une question (supprime les anciens, crée les nouveaux).
     * $choixData = [['libelle' => '...', 'valeur' => '...'], ...]
     */
    public function syncChoix(Question $question, array $choixData): void
    {
        $question->choix()->delete();

        foreach ($choixData as $index => $choix) {
            if (empty(trim($choix['valeur'] ?? ''))) {
                continue;
            }

            ChoixQuestion::create([
                'question_id' => $question->id,
                'libelle'     => $choix['libelle'] ?? $choix['valeur'],
                'valeur'      => $choix['valeur'],
                'ordre'       => $choix['ordre'] ?? ($index + 1),
            ]);
        }
    }
}
