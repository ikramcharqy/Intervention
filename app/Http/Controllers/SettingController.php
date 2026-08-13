<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'company_name'      => Setting::get('company_name', 'InterventionPRO Services'),
            'company_email'     => Setting::get('company_email', 'contact@interventionpro.ma'),
            'company_phone'     => Setting::get('company_phone', '+212 5 22 00 11 22'),
            'company_address'   => Setting::get('company_address', 'Casablanca Finance City, Maroc'),
            'currency'          => Setting::get('currency', 'MAD'),
            'gps_interval'      => Setting::get('gps_interval', '30'),
            'auto_validate_gps' => Setting::get('auto_validate_gps', '1'),
            'email_notifications' => Setting::get('email_notifications', '1'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'company_email'       => ['required', 'email', 'max:255'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'company_address'     => ['nullable', 'string', 'max:500'],
            'currency'            => ['required', 'string', 'max:10'],
            'gps_interval'        => ['required', 'integer', 'min:5', 'max:300'],
            'auto_validate_gps'   => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '0');
        }

        return redirect()->route('settings.index')->with('success', 'Les paramètres généraux du système ont été mis à jour avec succès.');
    }
}
