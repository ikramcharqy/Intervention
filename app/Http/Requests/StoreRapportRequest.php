<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRapportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $intervention = $this->route('intervention');
        return $this->user()->can('submitForm', $intervention) || $this->user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    public function rules(): array
    {
        // Étape 6 du prompt "Corrections critiques Paramètres" : extensions autorisées
        // pilotées par les réglages Super Admin (page Paramètres & Configurations APIs)
        // plutôt que figées en dur — la limite de taille reste par ailleurs telle quelle
        // (déjà existante avant ce prompt, hors périmètre demandé ici).
        $photoFormats = Setting::get('allowed_photo_formats', 'jpg,jpeg,png,webp');
        $videoFormats = Setting::get('allowed_video_formats', 'mp4,mov,avi,webm');

        return [
            'travaux_effectues'    => 'nullable|string',
            'observations'         => 'nullable|string',
            'recommandations'      => 'nullable|string',
            'statut_equipement'    => 'nullable|string',
            'qrcode_scanne'        => 'nullable|string',
            'commentaire'          => 'nullable|string',
            'gps_latitude'         => 'nullable|numeric',
            'gps_longitude'        => 'nullable|numeric',
            'gps_adresse'          => 'nullable|string',
            'date_debut'           => 'nullable|date',
            'date_fin'             => 'nullable|date',
            'signature_technicien' => 'nullable|string',
            'signature_client'     => 'nullable|string',
            'photos.*'             => "nullable|image|mimes:{$photoFormats}|max:10240",
            'photos_types.*'       => 'nullable|string',
            'videos.*'             => "nullable|file|mimes:{$videoFormats}|max:51200",
            'documents.*'          => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,txt|max:20480',
            'signature_technicien_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'signature_client_file'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
