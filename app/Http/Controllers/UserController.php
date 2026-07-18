<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Gestion des utilisateurs de la plateforme.
 */
class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Liste paginée des utilisateurs.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telephone', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->role($role);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('users.index', compact('users', 'roles', 'search', 'role'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        return view('users.create', compact('roles'));
    }

    /**
     * Enregistre un utilisateur.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', "L'utilisateur **{$user->prenom} {$user->name}** a été créé avec succès.");
    }

    /**
     * Détails d'un utilisateur.
     */
    public function show(User $user): View
    {
        $user->load(['roles', 'interventions' => fn($q) => $q->orderBy('date_prevue_debut', 'desc')->limit(10)]);
        return view('users.show', compact('user'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get(['id', 'name']);
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('success', "Le compte de **{$user->prenom} {$user->name}** a été mis à jour.");
    }

    /**
     * Désactive logiquement.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', "Vous ne pouvez pas désactiver votre propre compte.");
        }

        $this->userService->deactivate($user);

        return redirect()
            ->route('users.index')
            ->with('success', "L'utilisateur **{$user->prenom} {$user->name}** a été désactivé.");
    }

    /**
     * Réactive.
     */
    public function restore(User $user): RedirectResponse
    {
        $this->userService->activate($user);

        return redirect()
            ->route('users.show', $user)
            ->with('success', "L'utilisateur **{$user->prenom} {$user->name}** a été réactivé.");
    }
}
