<?php

namespace Database\Seeders;

use App\Models\Formulaire;
use App\Models\Intervention;
use App\Models\Question;
use App\Models\Rapport;
use App\Services\RemplissageFormulaireService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

/**
 * Filet de sécurité pour les données de démo/dev : toute intervention
 * "Terminée" sans Rapport associé est une impasse UX (l'écran de détail
 * propose "Voir le rapport" → 404 "Aucun rapport enregistré"). Contrairement
 * aux autres seeders de ce projet, celui-ci ne cible pas des lignes précises
 * par référence — il interroge la base pour TOUTE intervention dans cet état
 * incohérent (qu'elle vienne de TechnicienDemoDataSeeder, d'un autre seeder,
 * ou d'une création manuelle via l'app pendant le développement), donc
 * rejouable sans danger et robuste à de futures interventions orphelines.
 *
 * Réutilise RemplissageFormulaireService::sauvegarderReponses() — la même
 * logique que la vraie soumission du Formulaire dynamique technicien — pour
 * générer des réponses réalistes, plutôt que d'insérer des lignes `Reponse`
 * à la main dans un format qui pourrait diverger de la structure réelle.
 *
 * N'écrit JAMAIS sur une intervention qui a déjà un Rapport — ne touche donc
 * jamais aux interventions déjà correctement seedées (ex: INT-SA-024, dont
 * l'historique/rapport vient d'un vrai passage dans l'app).
 */
class OrphanedTermineeRapportSeeder extends Seeder
{
    public function run(): void
    {
        $interventions = Intervention::where('statut', Intervention::STATUT_TERMINEE)
            ->whereDoesntHave('rapport')
            ->with(['typeIntervention', 'chantier', 'emplacement', 'technicien'])
            ->get();

        foreach ($interventions as $intervention) {
            $formulaire = Formulaire::with(['questions.choix'])
                ->where('type_intervention_id', $intervention->type_intervention_id)
                ->where('is_active', true)
                ->first();

            if (!$formulaire || $formulaire->questions->isEmpty()) {
                // Pas de Formulaire exploitable pour ce TypeIntervention — on ne
                // peut pas générer de réponses réalistes ; signalé plutôt
                // qu'improvisé (une intervention sans Formulaire du tout est un
                // cas à traiter séparément, hors du périmètre de ce correctif).
                $this->command?->warn("Aucun Formulaire actif pour {$intervention->code_intervention} — rapport non généré.");
                continue;
            }

            $this->genererRapportRealiste($intervention, $formulaire);
            $this->completerHistorique($intervention);
        }
    }

    private function genererRapportRealiste(Intervention $intervention, Formulaire $formulaire): void
    {
        [$data, $files] = $this->construireReponses($intervention, $formulaire->questions);

        app(RemplissageFormulaireService::class)->sauvegarderReponses($intervention, $formulaire, $data, $files);

        $debut = $intervention->date_reelle_debut ?? $intervention->date_prevue_debut ?? now()->subHours(2);
        $fin = $intervention->date_reelle_fin ?? $debut->copy()->addMinutes($intervention->duree_prevue ?? 60);

        Rapport::where('intervention_id', $intervention->id)->update([
            'date_debut' => $debut,
            'date_fin' => $fin,
            'travaux_effectues' => 'Diagnostic complet, opérations correctives réalisées conformément à la checklist terrain, tests de fonctionnement validés.',
            'observations' => 'RAS — intervention réalisée sans incident.',
            'statut_equipement' => 'Conforme',
            'commentaire' => 'Formulaire terrain complété par le technicien.',
            'duree_reelle' => $intervention->duree_reelle ?? $debut->diffInMinutes($fin),
            'pourcentage_global' => 100,
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection<int, Question> $questions
     * @return array{0: array<int, mixed>, 1: array<int, mixed>}
     */
    private function construireReponses(Intervention $intervention, $questions): array
    {
        $data = [];
        $files = [];
        $dateRef = ($intervention->date_reelle_debut ?? $intervention->date_prevue_debut ?? now())->copy();

        [$lat, $lng] = $this->positionPlausible($intervention);

        foreach ($questions as $question) {
            switch ($question->type_reponse) {
                case 'Materiaux':
                    break; // Géré par les endpoints matériaux dédiés, jamais par le Formulaire.

                case 'Texte':
                    $data[$question->id] = $question->placeholder ?: 'Conforme, vérifié sur site.';
                    break;

                case 'TexteLong':
                    $data[$question->id] = 'Intervention réalisée sans incident. Tous les points de contrôle prévus par la checklist ont été vérifiés et sont conformes.';
                    break;

                case 'Nombre':
                    $min = $question->nombre_min;
                    $max = $question->nombre_max;
                    $data[$question->id] = $min !== null && $max !== null ? round(($min + $max) / 2, 1) : 1;
                    break;

                case 'Date':
                    $data[$question->id] = $dateRef->format('Y-m-d');
                    break;

                case 'Heure':
                    $data[$question->id] = $dateRef->format('H:i');
                    break;

                case 'DateHeure':
                    $data[$question->id] = $dateRef->format('Y-m-d H:i');
                    break;

                case 'OuiNon':
                    $data[$question->id] = 'Oui';
                    break;

                case 'Liste':
                case 'Radio':
                    $choix = $question->choix->first();
                    if ($choix) {
                        $data[$question->id] = $choix->id;
                    }
                    break;

                case 'Checkbox':
                    $choixIds = $question->choix->take(2)->pluck('id')->all();
                    if (!empty($choixIds)) {
                        $data[$question->id] = $choixIds;
                    }
                    break;

                case 'Photo':
                    $files[$question->id] = UploadedFile::fake()->image('photo_terrain.jpg', 1024, 768);
                    break;

                case 'Signature':
                    $files[$question->id] = UploadedFile::fake()->image('signature.png', 400, 150);
                    break;

                case 'Document':
                    $files[$question->id] = UploadedFile::fake()->create('fiche_intervention.pdf', 120, 'application/pdf');
                    break;

                case 'GPS':
                    $data[$question->id] = "{$lat},{$lng}";
                    break;

                case 'QRCode':
                    $data[$question->id] = $intervention->emplacement->qr_code ?? 'EMP-INCONNU';
                    break;
            }
        }

        return [$data, $files];
    }

    /**
     * Position réaliste : emplacement si géolocalisé, sinon chantier, avec un
     * léger bruit pour ne pas afficher une coordonnée pile identique à celle
     * du chantier sur chaque rapport généré.
     *
     * @return array{0: float, 1: float}
     */
    private function positionPlausible(Intervention $intervention): array
    {
        $base = $intervention->emplacement?->latitude && $intervention->emplacement?->longitude
            ? [(float) $intervention->emplacement->latitude, (float) $intervention->emplacement->longitude]
            : [(float) ($intervention->chantier->latitude ?? 33.5731), (float) ($intervention->chantier->longitude ?? -7.5898)];

        $jitter = fn (float $v) => round($v + (mt_rand(-50, 50) / 100000), 6);

        return [$jitter($base[0]), $jitter($base[1])];
    }

    /**
     * Cycle complet Planifiée → Acceptée → En cours → Formulaire rempli →
     * Terminée, avec des horodatages plausibles ancrés sur les dates réelles
     * déjà présentes sur l'intervention — même structure/colonnes que
     * InterventionService::enregistrerHistorique(), pas un format distinct.
     */
    private function completerHistorique(Intervention $intervention): void
    {
        $debut = $intervention->date_reelle_debut ?? $intervention->date_prevue_debut ?? now()->subHours(2);
        $fin = $intervention->date_reelle_fin ?? $debut->copy()->addMinutes($intervention->duree_prevue ?? 60);

        $acceptee = $debut->copy()->subHours(20);
        $planifiee = $acceptee->copy()->subDay();
        $formulaireRempli = $fin->copy()->subMinutes(3);

        $etapes = [
            [null, Intervention::STATUT_PLANIFIEE, 'Intervention planifiée et affectée au technicien.', $planifiee],
            [Intervention::STATUT_PLANIFIEE, Intervention::STATUT_ACCEPTEE, 'Mission acceptée par le technicien.', $acceptee],
            [Intervention::STATUT_ACCEPTEE, Intervention::STATUT_EN_COURS, 'Intervention démarrée sur site.', $debut],
            [Intervention::STATUT_EN_COURS, Intervention::STATUT_FORM_REMPLI, 'Saisie / Mise à jour du formulaire terrain par le technicien.', $formulaireRempli],
            [Intervention::STATUT_FORM_REMPLI, Intervention::STATUT_TERMINEE, "L'intervention a été terminée et transmise à l'administration par le technicien. Formulaire et rapport scellés.", $fin],
        ];

        foreach ($etapes as [$avant, $apres, $commentaire, $date]) {
            /** @var Carbon $date */
            $historique = $intervention->historiques()->create([
                'user_id' => $intervention->technicien_id,
                // '—' pour la toute première étape (pas de statut précédent) —
                // même convention que InterventionService::enregistrerHistorique(),
                // la colonne `statut_avant` étant NOT NULL.
                'statut_avant' => $avant ?? '—',
                'statut_apres' => $apres,
                'commentaire' => $commentaire,
            ]);
            $historique->forceFill(['created_at' => $date, 'updated_at' => $date])->save();
        }
    }
}
