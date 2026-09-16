<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'    => ['required', 'string', 'max:255'],
            'company_tagline' => ['nullable', 'string', 'max:255'],
            'company_email'   => ['required', 'email', 'max:255'],
            'company_phone'   => ['nullable', 'string', 'max:50'],
            'company_fax'     => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_website' => ['nullable', 'string', 'max:255'],
            // Étape 5.2 : ICE marocain — toujours 15 chiffres.
            'company_ice'     => ['nullable', 'digits:15'],
            // RC : format libre selon la juridiction, mais doit contenir un numéro
            // d'enregistrement reconnaissable (au moins 4 chiffres consécutifs).
            'company_rc'      => ['nullable', 'string', 'max:50', 'regex:/\d{4,}/'],
            'company_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'company_logo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_ice.digits'   => 'Le numéro ICE doit contenir exactement 15 chiffres.',
            'company_rc.regex'     => 'Le Registre du Commerce doit contenir un numéro d\'enregistrement (au moins 4 chiffres).',
            'company_color.regex'  => 'La couleur doit être un code hexadécimal valide (ex: #4338CA).',
            'company_logo.image'   => 'Le logo doit être une image (JPEG, PNG, SVG ou WebP).',
        ];
    }
}
