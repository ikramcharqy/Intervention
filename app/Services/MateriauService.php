<?php

namespace App\Services;

use App\Models\Materiau;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MateriauService
{
    public function __construct(protected StockService $stockService)
    {
    }

    /**
     * Enregistre un matériau.
     */
    public function createMateriau(array $data): Materiau
    {
        if (empty($data['reference'])) {
            $data['reference'] = $this->genererReference($data['nom']);
        }

        $data['qr_code'] = $this->genererQrCodeUnique();

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('materiaux', 'public');
        }
        unset($data['image']);

        if (isset($data['fiche_technique']) && $data['fiche_technique'] instanceof UploadedFile) {
            $data['fiche_technique_path'] = $data['fiche_technique']->store('materiaux/fiches-techniques', 'public');
        }
        unset($data['fiche_technique']);

        return Materiau::create($data);
    }

    /**
     * Met à jour un matériau. Le stock n'est jamais modifié ici : il ne
     * change que via consommation (validation d'intervention) ou
     * réapprovisionnement, pour garder une traçabilité complète.
     * Le QR code n'est jamais régénéré : c'est un identifiant physique
     * fixe une fois imprimé/collé sur le matériel.
     */
    public function updateMateriau(Materiau $materiau, array $data): Materiau
    {
        unset($data['stock'], $data['qr_code']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($materiau->image_path) {
                Storage::disk('public')->delete($materiau->image_path);
            }
            $data['image_path'] = $data['image']->store('materiaux', 'public');
        }
        unset($data['image']);

        if (isset($data['fiche_technique']) && $data['fiche_technique'] instanceof UploadedFile) {
            if ($materiau->fiche_technique_path) {
                Storage::disk('public')->delete($materiau->fiche_technique_path);
            }
            $data['fiche_technique_path'] = $data['fiche_technique']->store('materiaux/fiches-techniques', 'public');
        }
        unset($data['fiche_technique']);

        $materiau->update($data);
        return $materiau;
    }

    /**
     * Gère la suppression ou la désactivation si elle est déjà utilisée.
     */
    public function deleteOrDeactivate(Materiau $materiau): bool
    {
        if ($materiau->interventions()->exists()) {
            $materiau->update(['is_active' => false]);
            return false; // Désactivée
        }

        $materiau->delete();
        return true; // Supprimée
    }

    /**
     * Réapprovisionne le stock d'un matériau (Admin/Super Admin).
     */
    public function reapprovisionner(Materiau $materiau, float $quantite, ?string $commentaire, ?User $auteur): Materiau
    {
        $this->stockService->reapprovisionner($materiau, $quantite, $commentaire, $auteur);
        return $materiau->refresh();
    }

    /**
     * Génère une référence technique interne déterministe à partir du nom.
     */
    private function genererReference(string $nom): string
    {
        $slug = Str::of($nom)->ascii()->upper()->replaceMatches('/[^A-Z0-9]+/', '-')->trim('-');
        $suffixe = strtoupper(substr(uniqid(), -5));

        return "{$slug}-{$suffixe}";
    }

    /**
     * Génère un QR code logique unique pour le matériau (même convention
     * que EmplacementService::generateUniqueQrCode()).
     */
    private function genererQrCodeUnique(): string
    {
        do {
            $qrCode = 'MAT-QR-' . strtoupper(bin2hex(random_bytes(8)));
        } while (Materiau::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }
}
