<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBillingSettingsRequest;
use App\Http\Requests\UpdateBrandingSettingsRequest;
use App\Http\Requests\UpdateGpsSettingsRequest;
use App\Models\Setting;
use App\Services\SuperAdmin\PlatformSettingsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private PlatformSettingsService $settingsService)
    {
    }

    public function index(): View
    {
        return view('settings.index', [
            'branding' => $this->settingsService->getBranding(),
            'gps' => $this->settingsService->getGps(),
            'billing' => $this->settingsService->getBilling(),
            'paymentModeOptions' => PlatformSettingsService::PAYMENT_MODE_OPTIONS,
        ]);
    }

    // Étape 3 : une action de sauvegarde indépendante par sous-section, plutôt qu'un
    // unique bouton en bas d'une page monolithique.
    public function updateBranding(UpdateBrandingSettingsRequest $request): RedirectResponse
    {
        $this->settingsService->updateBranding($request->safe()->except('company_logo'), $request->file('company_logo'));

        return redirect()->route('settings.index')->with('success', 'Identité & Branding enregistrés avec succès.');
    }

    public function updateGps(UpdateGpsSettingsRequest $request): RedirectResponse
    {
        $this->settingsService->updateGps($request->validated());

        return redirect()->route('settings.index')->with('success', 'Paramètres GPS enregistrés avec succès.');
    }

    public function updateBilling(UpdateBillingSettingsRequest $request): RedirectResponse
    {
        $this->settingsService->updateBilling($request->validated());

        return redirect()->route('settings.index')->with('success', 'Facturation & conditions légales enregistrées avec succès.');
    }

    // Étape 6.1 : aperçu PDF généré à partir des valeurs actuellement saisies dans le
    // formulaire (non sauvegardées), sur un devis d'exemple — réutilise le même template
    // PDF que le module Devis (barryvdh/laravel-dompdf déjà en dépendance).
    public function previewPdf(Request $request)
    {
        $company = [
            'name' => $request->input('company_name', Setting::get('company_name')),
            'tagline' => $request->input('company_tagline', Setting::get('company_tagline')),
            'email' => $request->input('company_email', Setting::get('company_email')),
            'phone' => $request->input('company_phone', Setting::get('company_phone')),
            'fax' => $request->input('company_fax', Setting::get('company_fax')),
            'address' => $request->input('company_address', Setting::get('company_address')),
            'website' => $request->input('company_website', Setting::get('company_website')),
            'ice' => $request->input('company_ice', Setting::get('company_ice')),
            'rc' => $request->input('company_rc', Setting::get('company_rc')),
            'color' => $request->input('company_color', Setting::get('company_color', '#4338CA')),
            'logo' => Setting::get('company_logo'),
        ];

        $sample = (object) [
            'numero' => 'DEV-APERCU-0001',
            'date_emission' => now(),
            'client_nom' => 'Société Exemple SARL',
            'client_adresse' => '45, Rue de Démonstration, Casablanca',
            'lignes' => collect([
                (object) ['designation' => 'Intervention de maintenance (exemple)', 'quantite' => 1, 'prix_unitaire' => 1500, 'total' => 1500],
                (object) ['designation' => 'Déplacement technicien', 'quantite' => 1, 'prix_unitaire' => 250, 'total' => 250],
            ]),
            'montant_ht' => 1750,
            'montant_tva' => 350,
            'montant_ttc' => 2100,
        ];

        $pdf = Pdf::loadView('settings.pdf-preview', compact('company', 'sample'));

        return $pdf->stream('apercu-parametres.pdf');
    }
}
