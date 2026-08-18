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
            'company_name'      => Setting::get('company_name', 'TechInterv Solutions'),
            'company_tagline'   => Setting::get('company_tagline', 'Excellence en Maintenance & Interventions Techniques'),
            'company_email'     => Setting::get('company_email', 'contact@techinterv.ma'),
            'company_phone'     => Setting::get('company_phone', '+212 5 22 45 88 99'),
            'company_fax'       => Setting::get('company_fax', '+212 5 22 45 88 00'),
            'company_address'   => Setting::get('company_address', '12, Boulevard Hassan II – Casablanca, Maroc 20250'),
            'company_website'   => Setting::get('company_website', 'www.techinterv.ma'),
            'company_ice'       => Setting::get('company_ice', '002847593000088'),
            'company_rc'        => Setting::get('company_rc', 'RC 485920 – Casablanca'),
            'company_logo'      => Setting::get('company_logo', ''),
            'company_color'     => Setting::get('company_color', '#4338CA'),

            'currency'            => Setting::get('currency', 'MAD'),
            'gps_interval'        => Setting::get('gps_interval', '30'),
            'auto_validate_gps'   => Setting::get('auto_validate_gps', '1'),
            'email_notifications' => Setting::get('email_notifications', '1'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'company_tagline'     => ['nullable', 'string', 'max:255'],
            'company_email'       => ['required', 'email', 'max:255'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'company_fax'         => ['nullable', 'string', 'max:50'],
            'company_address'     => ['nullable', 'string', 'max:500'],
            'company_website'     => ['nullable', 'string', 'max:255'],
            'company_ice'         => ['nullable', 'string', 'max:50'],
            'company_rc'          => ['nullable', 'string', 'max:50'],
            'company_color'       => ['nullable', 'string', 'max:20'],
            'company_logo'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],

            'currency'            => ['required', 'string', 'max:10'],
            'gps_interval'        => ['required', 'integer', 'min:5', 'max:300'],
            'auto_validate_gps'   => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('company', 'public');
            Setting::set('company_logo', $path);
        }

        unset($validated['company_logo']);

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) ($value ?? ''));
        }

        return redirect()->route('settings.index')->with('success', 'Les coordonnées de la société et paramètres généraux ont été enregistrés avec succès.');
    }
}
