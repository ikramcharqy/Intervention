<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Formulaire;
use App\Http\Requests\RemplissageFormulaireRequest;
use App\Services\RemplissageFormulaireService;
use App\Services\InterventionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InterventionFormulaireController extends Controller
{
    protected RemplissageFormulaireService $service;
    protected InterventionService $interventionService;

    public function __construct(RemplissageFormulaireService $service, InterventionService $interventionService)
    {
        $this->service = $service;
        $this->interventionService = $interventionService;
    }

    /**
     * Affiche le formulaire dynamique à remplir pour l'intervention.
     */
    public function create(Intervention $intervention): View|RedirectResponse
    {
        // Vérifier si l'intervention a un formulaire actif
        $formulaire = $intervention->typeIntervention?->formulaire;

        if (!$formulaire || !$formulaire->is_active) {
            return redirect()->route('interventions.show', $intervention)
                ->with('error', 'Aucun formulaire actif associé à cette intervention.');
        }

        // Si l'intervention est terminée, elle est en lecture seule
        $isReadOnly = $intervention->statut === 'Terminee';

        // Charger les questions avec leurs choix
        $formulaire->load(['questions' => function($q) {
            $q->orderBy('ordre');
        }, 'questions.choix']);

        // Pré-charger les réponses existantes s'il y en a (via le rapport)
        $reponsesExistantes = [];
        if ($intervention->rapport) {
            $intervention->rapport->load('reponses');
            foreach ($intervention->rapport->reponses as $rep) {
                if ($rep->choix_question_id) {
                    // Pour les Checkbox (plusieurs choix possibles)
                    if (!isset($reponsesExistantes[$rep->question_id])) {
                        $reponsesExistantes[$rep->question_id] = [];
                    }
                    if (is_array($reponsesExistantes[$rep->question_id])) {
                        $reponsesExistantes[$rep->question_id][] = $rep->choix_question_id;
                    } else {
                        // Pour Radio/Liste (un seul choix, mais on s'assure de l'écraser si besoin)
                        $reponsesExistantes[$rep->question_id] = $rep->choix_question_id;
                    }
                } elseif ($rep->reponse_nombre !== null) {
                    $reponsesExistantes[$rep->question_id] = $rep->reponse_nombre;
                } elseif ($rep->reponse_fichier !== null) {
                    $reponsesExistantes[$rep->question_id] = $rep->reponse_fichier;
                } else {
                    $reponsesExistantes[$rep->question_id] = $rep->reponse_texte;
                }
            }
        }

        return view('interventions.formulaire', compact('intervention', 'formulaire', 'isReadOnly', 'reponsesExistantes'));
    }

    /**
     * Sauvegarde les données soumises.
     */
    public function store(RemplissageFormulaireRequest $request, Intervention $intervention): RedirectResponse
    {
        if ($intervention->statut === 'Terminee') {
            return redirect()->route('interventions.show', $intervention)
                ->with('error', 'L\'intervention est terminée. Le formulaire est en lecture seule.');
        }

        $formulaire = $intervention->typeIntervention?->formulaire;

        if (!$formulaire || !$formulaire->is_active) {
            return redirect()->route('interventions.show', $intervention)
                ->with('error', 'Erreur: Aucun formulaire actif trouvé.');
        }

        // Validation est gérée par RemplissageFormulaireRequest
        $data = $request->input('reponses', []);
        $files = $request->file('reponses', []);

        $this->service->sauvegarderReponses($intervention, $formulaire, $data, $files);

        // Faire passer l'intervention automatiquement à l'état 'Formulaire rempli'
        $this->interventionService->submitForm($intervention);

        return redirect()->route('interventions.show', $intervention)
            ->with('success', 'Le formulaire a été sauvegardé avec succès et soumis pour validation.');
    }
}
