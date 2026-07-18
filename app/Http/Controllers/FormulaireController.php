<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormulaireRequest;
use App\Http\Requests\UpdateFormulaireRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Formulaire;
use App\Models\Question;
use App\Models\TypeIntervention;
use App\Services\FormulaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormulaireController extends Controller
{
    protected FormulaireService $formulaireService;

    public function __construct(FormulaireService $formulaireService)
    {
        $this->formulaireService = $formulaireService;
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Formulaires
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $search = $request->input('search');

        $formulaires = Formulaire::with('typeIntervention')
            ->withCount('questions')
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhereHas('typeIntervention', fn($q) => $q->where('nom', 'like', "%{$search}%"));
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('formulaires.index', compact('formulaires', 'search'));
    }

    public function create(): View
    {
        // On ne propose que les types d'intervention n'ayant pas encore de formulaire (sauf si on édite)
        $typesIntervention = TypeIntervention::where('is_active', true)
            ->whereDoesntHave('formulaire')
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('formulaires.create', compact('typesIntervention'));
    }

    public function store(StoreFormulaireRequest $request): RedirectResponse
    {
        $formulaire = $this->formulaireService->createFormulaire($request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Formulaire créé avec succès. Vous pouvez maintenant y ajouter des champs.');
    }

    public function show(Formulaire $formulaire): View
    {
        $formulaire->load([
            'typeIntervention',
            'questions.choix'
        ]);

        return view('formulaires.show', compact('formulaire'));
    }

    public function edit(Formulaire $formulaire): View
    {
        // On propose le type actuel + les types sans formulaire
        $typesIntervention = TypeIntervention::where('is_active', true)
            ->where(function ($query) use ($formulaire) {
                $query->whereDoesntHave('formulaire')
                      ->orWhere('id', $formulaire->type_intervention_id);
            })
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('formulaires.edit', compact('formulaire', 'typesIntervention'));
    }

    public function update(UpdateFormulaireRequest $request, Formulaire $formulaire): RedirectResponse
    {
        $this->formulaireService->updateFormulaire($formulaire, $request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Formulaire mis à jour avec succès.');
    }

    public function destroy(Formulaire $formulaire): RedirectResponse
    {
        $this->formulaireService->deleteFormulaire($formulaire);

        return redirect()
            ->route('formulaires.index')
            ->with('success', 'Formulaire supprimé avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD Questions (Champs)
    |--------------------------------------------------------------------------
    */

    public function storeQuestion(StoreQuestionRequest $request, Formulaire $formulaire): RedirectResponse
    {
        $this->formulaireService->addQuestion($formulaire, $request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Champ ajouté avec succès.');
    }

    public function updateQuestion(UpdateQuestionRequest $request, Formulaire $formulaire, Question $question): RedirectResponse
    {
        if ($question->formulaire_id !== $formulaire->id) {
            abort(404);
        }

        $this->formulaireService->updateQuestion($question, $request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Champ mis à jour avec succès.');
    }

    public function destroyQuestion(Formulaire $formulaire, Question $question): RedirectResponse
    {
        if ($question->formulaire_id !== $formulaire->id) {
            abort(404);
        }

        $this->formulaireService->deleteQuestion($question);

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Champ supprimé avec succès.');
    }

    public function reorderQuestions(Request $request, Formulaire $formulaire): RedirectResponse
    {
        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'required|integer|min:1',
        ]);

        $this->formulaireService->reorderQuestions($formulaire, $request->input('ordre'));

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Ordre des champs mis à jour avec succès.');
    }
}
