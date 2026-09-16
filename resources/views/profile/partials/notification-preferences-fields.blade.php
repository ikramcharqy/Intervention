{{-- Champs "Préférences de notification" : inclus dans le <form> unique de profile/edit.blade.php --}}
@php
    $prefs = $user->notification_preferences ?? [];
    $isCommercial = $user->hasRole('Commercial');
    $isTechnicien = $user->hasAnyRole(['technicien', 'Technicien']);
@endphp

<div class="space-y-3">
    @if($isTechnicien || $user->hasAnyRole(['Super Admin', 'admin']))
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="notification_preferences[intervention_updates]" value="1" @checked($prefs['intervention_updates'] ?? true)
                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Mises à jour d'intervention</span>
                <span class="block text-xs text-gray-400">Nouvelle intervention affectée, modification, réaffectation...</span>
            </span>
        </label>
    @endif

    @if($isCommercial)
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="notification_preferences[nouveaux_prospects_assignes]" value="1" @checked($prefs['nouveaux_prospects_assignes'] ?? true)
                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Nouveaux prospects assignés</span>
                <span class="block text-xs text-gray-400">Un prospect vous est attribué par un administrateur.</span>
            </span>
        </label>

        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="notification_preferences[relances_devis]" value="1" @checked($prefs['relances_devis'] ?? true)
                class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span>
                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Relances de devis</span>
                <span class="block text-xs text-gray-400">Un devis envoyé approche de sa date d'expiration sans réponse.</span>
            </span>
        </label>

        <p class="text-[11px] text-amber-600 dark:text-amber-400 mt-2">
            ⚠️ Ces deux préférences seront appliquées dès que les notifications correspondantes seront mises en place dans l'outil — elles sont enregistrées dès maintenant pour ne pas avoir à les reconfigurer plus tard.
        </p>
    @endif
</div>
