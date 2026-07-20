<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Emplacement réservé pour le futur module "Documents" côté Commercial.
 *
 * Il existe déjà un modèle Document, mais uniquement rattaché aux rapports
 * d'intervention — il n'y a pas encore d'espace documentaire dédié au
 * Commercial. Ce contrôleur affiche uniquement une page d'attente afin que
 * l'entrée de menu "Documents" du Dashboard Commercial soit fonctionnelle,
 * sans anticiper l'implémentation de la fonctionnalité métier elle-même.
 */
class DocumentController extends Controller
{
    public function index(): View
    {
        return view('commercial.documents.index');
    }
}
