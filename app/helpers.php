<?php

use App\Models\Setting;

if (! function_exists('currency_symbol')) {
    /**
     * Symbole de la devise configurée globalement (Paramètres > Système), centralisé
     * ici pour n'avoir qu'un seul endroit à changer (le projet affichait auparavant
     * "MAD", "DH" et "€" à différents endroits selon le module).
     */
    function currency_symbol(): string
    {
        $symbols = ['MAD' => 'DH', 'EUR' => '€', 'USD' => '$'];

        return $symbols[Setting::get('currency', 'MAD')] ?? 'DH';
    }
}

if (! function_exists('format_montant')) {
    function format_montant(float|int|null $montant, int $decimals = 2): string
    {
        return number_format((float) $montant, $decimals, ',', ' ') . ' ' . currency_symbol();
    }
}
