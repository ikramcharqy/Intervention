<?php

namespace App\Services\SuperAdmin;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;

/**
 * Étape 3 : la page "Paramètres du Système" scindée en 3 domaines indépendants
 * (Identité & Branding / Opérations & Télémétrie GPS / Facturation & Conditions
 * Légales) — chaque domaine a sa propre méthode de lecture/écriture et sa propre
 * entrée de journalisation, au lieu d'un unique formulaire monolithique.
 */
class PlatformSettingsService
{
    public const PAYMENT_MODE_OPTIONS = [
        'virement' => 'Virement bancaire',
        'cheque'   => 'Chèque',
        'especes'  => 'Espèces',
        'carte'    => 'Carte bancaire',
    ];

    public function getBranding(): array
    {
        return [
            'company_name'    => Setting::get('company_name', 'TechInterv Solutions'),
            'company_tagline' => Setting::get('company_tagline', 'Excellence en Maintenance & Interventions Techniques'),
            'company_email'   => Setting::get('company_email', 'contact@techinterv.ma'),
            'company_phone'   => Setting::get('company_phone', '+212 5 22 45 88 99'),
            'company_fax'     => Setting::get('company_fax', '+212 5 22 45 88 00'),
            'company_address' => Setting::get('company_address', '12, Boulevard Hassan II – Casablanca, Maroc 20250'),
            'company_website' => Setting::get('company_website', 'www.techinterv.ma'),
            'company_ice'     => Setting::get('company_ice', '002847593000088'),
            'company_rc'      => Setting::get('company_rc', 'RC 485920 – Casablanca'),
            'company_logo'    => Setting::get('company_logo', ''),
            'company_color'   => Setting::get('company_color', '#4338CA'),
        ];
    }

    public function getGps(): array
    {
        return [
            'gps_interval'           => (int) Setting::get('gps_interval', '30'),
            'auto_validate_gps'      => (bool) Setting::get('auto_validate_gps', '1'),
            'gps_validation_radius'  => (int) Setting::get('gps_validation_radius', '100'),
            'email_notifications'    => (bool) Setting::get('email_notifications', '1'),
        ];
    }

    public function getBilling(): array
    {
        $paymentModeRaw = Setting::get('payment_mode', json_encode(['virement', 'cheque', 'especes']));
        $paymentMode = json_decode($paymentModeRaw, true);

        return [
            'currency'                => Setting::get('currency', 'MAD'),
            'payment_mode'            => is_array($paymentMode) ? $paymentMode : [],
            'payment_delay_days'      => (int) Setting::get('payment_delay_days', '30'),
            'payment_deposit_percent' => (float) Setting::get('payment_deposit_percent', '0'),
            'legal_mentions'          => Setting::get('legal_mentions', ''),
        ];
    }

    public function updateBranding(array $data, ?UploadedFile $logo): void
    {
        $changed = [];

        foreach (['company_name', 'company_tagline', 'company_email', 'company_phone', 'company_fax', 'company_address', 'company_website', 'company_ice', 'company_rc', 'company_color'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, (string) ($data[$key] ?? ''));
                $changed[] = $key;
            }
        }

        if ($logo) {
            $path = $logo->store('company', 'public');
            Setting::set('company_logo', $path);
            $changed[] = 'company_logo';
        }

        $this->logChange('Identité & Branding', $changed);
    }

    public function updateGps(array $data): void
    {
        Setting::set('gps_interval', (string) $data['gps_interval']);
        Setting::set('auto_validate_gps', $data['auto_validate_gps'] ?? false ? '1' : '0');
        Setting::set('gps_validation_radius', (string) ($data['gps_validation_radius'] ?? 100));
        Setting::set('email_notifications', $data['email_notifications'] ?? false ? '1' : '0');

        $this->logChange('Opérations & Télémétrie GPS', array_keys($data));
    }

    public function updateBilling(array $data): void
    {
        Setting::set('currency', $data['currency']);
        Setting::set('payment_mode', json_encode(array_values($data['payment_mode'] ?? [])));
        Setting::set('payment_delay_days', (string) $data['payment_delay_days']);
        Setting::set('payment_deposit_percent', (string) $data['payment_deposit_percent']);
        Setting::set('legal_mentions', (string) ($data['legal_mentions'] ?? ''));

        $this->logChange('Facturation & Conditions Légales', array_keys($data));
    }

    /**
     * Étape 6.2 : toute sauvegarde de paramètre à portée globale est journalisée dans le
     * Journal de Sécurité & Gouvernance (identité de l'acteur, champs modifiés, horodatage).
     */
    private function logChange(string $section, array $fields): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Modification des paramètres « {$section} » : " . implode(', ', $fields),
            'module' => 'Settings',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'SUCCESS',
            'ip_address' => request()->ip(),
        ]);
    }
}
