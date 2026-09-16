<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormulaireRequest;
use App\Http\Requests\UpdateFormulaireRequest;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Http\Requests\UpdateQuestionChoixRequest;
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

    /**
     * Seul le Super Admin peut créer/modifier la structure des protocoles
     * (TypeIntervention, Formulaire, Questions). L'Admin ne peut gérer que
     * les choix des questions à choix multiples existantes (cf. updateQuestionChoix).
     */
    private function ensureSuperAdmin(): void
    {
        if (!auth()->user()?->hasRole('Super Admin')) {
            abort(403, "Cette action est réservée au Super Admin.");
        }
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

        // Vue partagée Super Admin / Admin : layout choisi dynamiquement pour ne rien
        // changer à l'expérience Admin existante (cf. migration design system Super Admin).
        $layoutComponent = auth()->user()->hasRole('Super Admin') ? 'super-admin-layout' : 'app-layout';

        return view('formulaires.index', compact('formulaires', 'search', 'layoutComponent'));
    }

    public function create(Request $request): View
    {
        $this->ensureSuperAdmin();

        // On ne propose que les types d'intervention n'ayant pas encore de formulaire (sauf si on édite)
        $typesIntervention = TypeIntervention::where('is_active', true)
            ->whereDoesntHave('formulaire')
            ->orderBy('nom')
            ->get(['id', 'nom']);

        // Étape 3.2 (liste des Types d'Intervention) : arrivée directe depuis l'indicateur
        // "Formulaire non configuré" avec le type déjà présélectionné.
        $preselectedTypeId = $request->integer('type_intervention_id') ?: null;

        return view('formulaires.create', compact('typesIntervention', 'preselectedTypeId'));
    }

    public function store(StoreFormulaireRequest $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $formulaire = $this->formulaireService->createFormulaire($request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Étape 1 terminée. Ajoutez maintenant les questions du protocole ci-dessous.');
    }

    public function show(Formulaire $formulaire): View
    {
        $formulaire->load([
            'typeIntervention',
            'questions.choix'
        ]);

        $layoutComponent = auth()->user()->hasRole('Super Admin') ? 'super-admin-layout' : 'app-layout';

        return view('formulaires.show', compact('formulaire', 'layoutComponent'));
    }

    public function edit(Formulaire $formulaire): View
    {
        $this->ensureSuperAdmin();

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
        $this->ensureSuperAdmin();

        try {
            $this->formulaireService->updateFormulaire($formulaire, $request->validated());
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Formulaire mis à jour avec succès.');
    }

    public function destroy(Formulaire $formulaire): RedirectResponse
    {
        $this->ensureSuperAdmin();

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
        $this->ensureSuperAdmin();

        $this->formulaireService->addQuestion($formulaire, $request->validated());

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Champ ajouté avec succès.');
    }

    public function updateQuestion(UpdateQuestionRequest $request, Formulaire $formulaire, Question $question): RedirectResponse
    {
        $this->ensureSuperAdmin();

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
        $this->ensureSuperAdmin();

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
        $this->ensureSuperAdmin();

        $request->validate([
            'ordre'   => 'required|array',
            'ordre.*' => 'required|integer|min:1',
        ]);

        $this->formulaireService->reorderQuestions($formulaire, $request->input('ordre'));

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Ordre des champs mis à jour avec succès.');
    }

    /**
     * Gestion des choix d'une question existante à choix multiples (Liste/Radio/Checkbox).
     * Accessible à l'Admin ET au Super Admin : contrairement aux actions ci-dessus,
     * ceci ne modifie ni la structure de la question (libellé, type, obligatoire),
     * ni le formulaire — uniquement la liste des choix proposés.
     */
    public function updateQuestionChoix(UpdateQuestionChoixRequest $request, Formulaire $formulaire, Question $question): RedirectResponse
    {
        if (!auth()->user()?->hasAnyRole(['Super Admin', 'admin'])) {
            abort(403, "Cette action est réservée aux administrateurs.");
        }

        if ($question->formulaire_id !== $formulaire->id) {
            abort(404);
        }

        if (!$question->necessiteChoix()) {
            abort(422, "Ce champ n'accepte pas de choix.");
        }

        $this->formulaireService->syncChoix($question, $request->validated('choix', []));

        return redirect()
            ->route('formulaires.show', $formulaire)
            ->with('success', 'Choix mis à jour avec succès.');
    }
}
