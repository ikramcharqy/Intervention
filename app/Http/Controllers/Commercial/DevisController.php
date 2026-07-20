<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Emplacement réservé pour le futur module "Devis".
 *
 * Aucun modèle ni logique métier n'existe encore pour les devis dans
 * cette application : ce contrôleur affiche uniquement une page d'attente
 * afin que l'entrée de menu "Devis" du Dashboard Commercial soit fonctionnelle,
 * sans anticiper l'implémentation de la fonctionnalité métier elle-même.
 */
class DevisController extends Controller
{
    public function index(): View
    {
        return view('commercial.devis.index');
    }
}
