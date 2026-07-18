<?php

namespace App\Services;

use App\Models\Emplacement;

class EmplacementService
{
    /**
     * Enregistre un nouvel emplacement.
     */
    public function createEmplacement(array $data): Emplacement
    {
        // Génération automatique et unique du QR code
        $data['qr_code'] = $this->generateUniqueQrCode();

        return Emplacement::create($data);
    }

    /**
     * Met à jour un emplacement.
     */
    public function updateEmplacement(Emplacement $emplacement, array $data): Emplacement
    {
        $emplacement->update($data);
        return $emplacement;
    }

    /**
     * Désactive logiquement un emplacement.
     */
    public function deactivate(Emplacement $emplacement): void
    {
        $emplacement->update(['is_active' => false]);
    }

    /**
     * Réactive un emplacement.
     */
    public function activate(Emplacement $emplacement): void
    {
        $emplacement->update(['is_active' => true]);
    }

    /**
     * Génère un QR Code unique pour l'emplacement.
     */
    public function generateUniqueQrCode(): string
    {
        do {
            // Format du QR code logique unique
            $qrCode = 'EMP-QR-' . strtoupper(bin2hex(random_bytes(8)));
        } while (Emplacement::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }

    /*
    |--------------------------------------------------------------------------
    | Futures extensions (Compatibilité technique planifiée)
    |--------------------------------------------------------------------------
    |
    | 1. Génération de PNG :
    |    public function generatePng(Emplacement $emplacement) {
    |        // Utiliser une bibliothèque comme simplesoftwareio/simple-qrcode
    |        // return QrCode::format('png')->size(300)->generate($emplacement->qr_code);
    |    }
    |
    | 2. Étiquettes imprimables :
    |    public function printLabel(Emplacement $emplacement) {
    |        // Générer un layout PDF ou HTML adapté aux formats d'étiquettes Zebra/Avery
    |    }
    |
    | 3. Scanner QR Code mobile :
    |    public function validateScan(string $scannedCode): ?Emplacement {
    |        // Rechercher l'emplacement actif correspondant au code scanné
    |        // return Emplacement::where('qr_code', $scannedCode)->where('is_active', true)->first();
    |    }
    */
}
