<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;


    /**
     * Résout la vue à afficher selon le rôle de l'utilisateur connecté.
     *
     * Si l'utilisateur a le rôle "Commercial" et qu'une vue dédiée existe
     * sous resources/views/commercial/{$view}.blade.php, celle-ci est utilisée.
     * Sinon, la vue "classique" est retournée inchangée.
     *
     * Cette méthode ne fait que choisir le template à afficher : aucune
     * donnée ni logique métier n'est dupliquée ou modifiée.
     */
    protected function roleView(string $view, array $data = [])
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Commercial')) {
            $commercialView = 'commercial.'.$view;

            if (view()->exists($commercialView)) {
                return view($commercialView, $data);
            }
        }

        return view($view, $data);
    }
}
