<?php

namespace App\Http\Requests;

use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBillingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency'                => ['required', Rule::in(['MAD', 'EUR', 'USD'])],
            // Étape 5.3 : sélection structurée plutôt que texte libre concaténé.
            'payment_mode'            => ['required', 'array', 'min:1'],
            'payment_mode.*'          => [Rule::in(array_keys(PlatformSettingsService::PAYMENT_MODE_OPTIONS))],
            'payment_delay_days'      => ['nullable', 'integer', 'min:0', 'max:365'],
            'payment_deposit_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'legal_mentions'          => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_mode.required' => 'Sélectionnez au moins un mode de paiement accepté.',
        ];
    }
}
