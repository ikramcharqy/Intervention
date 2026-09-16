<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Chantier;
use App\Models\Emplacement;
use App\Models\Intervention;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EmplacementService
{
    public function __construct(private ReferenceGeneratorService $referenceGenerator)
    {
    }

    /**
     * Enregistre un nouvel emplacement.
     */
    public function createEmplacement(array $data): Emplacement
    {
        // Génération automatique et unique du QR code, via le point d'entrée centralisé
        // (ReferenceGeneratorService) plutôt qu'un format local propre à ce Service.
        $chantier = Chantier::findOrFail($data['chantier_id']);
        $data['qr_code'] = $this->generateUniqueQrCode($chantier);

        // Ajout en fin de liste si aucun ordre explicite n'est fourni
        if (empty($data['ordre_affichage'])) {
            $data['ordre_affichage'] = (int) (Emplacement::where('chantier_id', $data['chantier_id'])->max('ordre_affichage')) + 1;
        }

        return Emplacement::create($data);
    }

    /**
     * Régénère le QR Code d'un emplacement (perte/dégradation du support imprimé) et
     * journalise l'opération dans le Journal de Sécurité & Gouvernance — action à impact
     * réel sur le terrain (invalidation immédiate de l'ancien support).
     *
     * @return array{ancien: string, nouveau: string}
     */
    public function regenerateQrCode(Emplacement $emplacement): array
    {
        $ancien = $emplacement->qr_code;
        $nouveau = $this->generateUniqueQrCode($emplacement->chantier);

        $emplacement->update(['qr_code' => $nouveau]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name ?? 'System',
            'action' => "Régénération du QR Code de l'emplacement « {$emplacement->nom} » ({$ancien} → {$nouveau})",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);

        return ['ancien' => $ancien, 'nouveau' => $nouveau];
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
     * Bloque l'action si une intervention Planifiée ou En cours y est rattachée.
     *
     * @throws RuntimeException
     */
    public function deactivate(Emplacement $emplacement): void
    {
        $count = $emplacement->interventions()
            ->whereIn('statut', [Intervention::STATUT_PLANIFIEE, Intervention::STATUT_EN_COURS])
            ->count();

        if ($count > 0) {
            throw new RuntimeException(
                "Impossible de désactiver cet emplacement : {$count} intervention(s) planifiée(s) ou en cours y sont rattachée(s)."
            );
        }

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
     * Remonte un emplacement d'un rang dans l'ordre d'affichage du chantier.
     */
    public function moveUp(Emplacement $emplacement): void
    {
        $precedent = Emplacement::where('chantier_id', $emplacement->chantier_id)
            ->where('ordre_affichage', '<', $emplacement->ordre_affichage)
            ->orderByDesc('ordre_affichage')
            ->first();

        $this->swapOrdre($emplacement, $precedent);
    }

    /**
     * Descend un emplacement d'un rang dans l'ordre d'affichage du chantier.
     */
    public function moveDown(Emplacement $emplacement): void
    {
        $suivant = Emplacement::where('chantier_id', $emplacement->chantier_id)
            ->where('ordre_affichage', '>', $emplacement->ordre_affichage)
            ->orderBy('ordre_affichage')
            ->first();

        $this->swapOrdre($emplacement, $suivant);
    }

    private function swapOrdre(Emplacement $emplacement, ?Emplacement $voisin): void
    {
        if (! $voisin) {
            return;
        }

        DB::transaction(function () use ($emplacement, $voisin) {
            $ordreEmplacement = $emplacement->ordre_affichage;
            $emplacement->update(['ordre_affichage' => $voisin->ordre_affichage]);
            $voisin->update(['ordre_affichage' => $ordreEmplacement]);
        });
    }

    /**
     * Génère un QR Code (référence logique) unique pour l'emplacement, via le point
     * d'entrée centralisé ReferenceGeneratorService — remplace l'ancien format aléatoire
     * "EMP-QR-{hex}" propre à cette méthode, seul et même chemin désormais utilisé par
     * createEmplacement() et regenerateQrCode().
     */
    public function generateUniqueQrCode(Chantier $chantier): string
    {
        return $this->referenceGenerator->generateEmplacementReference($chantier);
    }

    /**
     * Rendu SVG (image vectorielle) du QR Code réel — pas seulement sa référence texte.
     * SVG choisi car ni l'extension GD ni Imagick ne sont disponibles sur cet environnement ;
     * dompdf (via php-svg-lib) sait aussi bien afficher ce même SVG dans l'export PDF groupé.
     */
    public function qrSvg(Emplacement $emplacement, int $size = 240): string
    {
        $renderer = new ImageRenderer(new RendererStyle($size, 1), new SvgImageBackEnd());

        return (new Writer($renderer))->writeString($emplacement->qr_code);
    }

    /**
     * Export PDF groupé de tous les QR Codes actifs d'un chantier (planche prête à
     * imprimer), sur le même principe que ChantierService::generatePdf /
     * DevisService::generatePdf (Pdf::loadView depuis le Service, pas le Controller).
     */
    public function exportQrCodesPdf(Chantier $chantier)
    {
        $emplacements = $chantier->emplacements()->orderBy('ordre_affichage')->get();

        $qrByEmplacement = $emplacements->mapWithKeys(
            fn (Emplacement $e) => [$e->id => base64_encode($this->qrSvg($e, 220))]
        );

        $company = [
            'name' => \App\Models\Setting::get('company_name', 'TechInterv Solutions'),
            'tagline' => \App\Models\Setting::get('company_tagline', ''),
            'address' => \App\Models\Setting::get('company_address', ''),
            'phone' => \App\Models\Setting::get('company_phone', ''),
            'email' => \App\Models\Setting::get('company_email', ''),
            'color' => \App\Models\Setting::get('company_color', '#10b981'),
        ];

        return Pdf::loadView('emplacements.qr-export-pdf', compact('chantier', 'emplacements', 'qrByEmplacement', 'company'));
    }
}
