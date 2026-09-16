<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'rapport_id',
        'question_id',
        'choix_question_id',

        'reponse_texte',
        'reponse_nombre',
        'reponse_fichier',

        // Nouveaux champs
        'reponse_date',
        'reponse_heure',
        'reponse_datetime',
        'reponse_boolean',
    ];

    protected $casts = [
        'reponse_date' => 'date',
        'reponse_heure' => 'datetime:H:i',
        'reponse_datetime' => 'datetime',
        'reponse_boolean' => 'boolean',
    ];

    /**
     * Sélection Checkbox multiple : au-delà d'un seul choix,
     * RemplissageFormulaireService::sauvegarderReponses() ne peut pas utiliser
     * `choix_question_id` (une seule colonne, une seule relation) et stocke
     * les identifiants bruts séparés par des virgules dans `reponse_texte`
     * (ex: "3,7,9"). Cet accesseur les résout vers leurs libellés réels
     * (`ChoixQuestion::valeur`) pour que les consommateurs de l'API (app
     * Technicien) n'aient jamais à afficher des identifiants bruts — même
     * logique que celle déjà utilisée côté web
     * (resources/views/components/rapport-formulaire.blade.php), centralisée
     * ici plutôt que réimplémentée par chaque client.
     */
    protected $appends = ['choix_labels'];

    public function getChoixLabelsAttribute(): ?array
    {
        if (!$this->relationLoaded('question') || $this->question?->type_reponse !== 'Checkbox') {
            return null;
        }
        if ($this->choix_question_id || empty($this->reponse_texte)) {
            return null;
        }

        $ids = collect(explode(',', $this->reponse_texte))
            ->map(fn ($id) => trim($id))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id);

        if ($ids->isEmpty()) {
            return null;
        }

        $choix = $this->relationLoaded('question') && $this->question->relationLoaded('choix')
            ? $this->question->choix
            : ChoixQuestion::whereIn('id', $ids)->get();

        $labels = $ids->map(fn ($id) => $choix->firstWhere('id', $id)?->valeur)->filter()->values();

        return $labels->isEmpty() ? null : $labels->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    // Rapport concerné
    public function rapport()
    {
        return $this->belongsTo(Rapport::class);
    }

    // Question concernée
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Choix sélectionné
    public function choixQuestion()
    {
        return $this->belongsTo(ChoixQuestion::class);
    }
}
