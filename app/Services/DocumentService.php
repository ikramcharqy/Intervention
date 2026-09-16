<?php

namespace App\Services;

use App\Models\Devis;
use App\Models\Facture;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function store(array $data, UploadedFile $file, int $userId): Document
    {
        $path = $file->store('documents', 'public');

        return Document::create([
            'user_id' => $userId,
            'prospect_id' => $data['prospect_id'] ?? null,
            'client_id' => $data['client_id'] ?? null,
            'devis_id' => $data['devis_id'] ?? null,
            'chantier_id' => $data['chantier_id'] ?? null,
            'type_document' => $data['type_document'],
            'nom_original' => $file->getClientOriginalName(),
            'chemin' => $path,
            'type_mime' => $file->getClientMimeType(),
        ]);
    }

    public function delete(Document $document): void
    {
        if (Storage::disk('public')->exists($document->chemin)) {
            Storage::disk('public')->delete($document->chemin);
        }

        $document->delete();
    }

    /**
     * Crée (ou met à jour, si déjà générée précédemment) l'entrée "Mes Documents"
     * correspondant au PDF d'un devis, à chaque génération. Idempotent sur
     * (devis_id, type_document="Devis") pour ne pas empiler une entrée à
     * chaque nouvelle consultation/téléchargement du même devis.
     */
    public function syncDevisPdf(Devis $devis, string $pdfBinary): Document
    {
        $filename = 'devis_' . ($devis->reference ?? $devis->id) . '.pdf';
        $existing = Document::where('devis_id', $devis->id)
            ->where('type_document', 'Devis')
            ->first();

        $path = $existing?->chemin ?? 'documents/' . uniqid('devis_') . '.pdf';
        Storage::disk('public')->put($path, $pdfBinary);

        return Document::updateOrCreate(
            ['devis_id' => $devis->id, 'type_document' => 'Devis'],
            [
                'user_id' => $devis->commercial_id,
                'client_id' => $devis->client_id,
                'nom_original' => $filename,
                'chemin' => $path,
                'type_mime' => 'application/pdf',
            ]
        );
    }

    /**
     * Équivalent de syncDevisPdf pour les factures : entrée idempotente dans
     * "Mes Documents" (Type=Facture) sur (facture_id, type_document="Facture").
     */
    public function syncFacturePdf(Facture $facture, string $pdfBinary): Document
    {
        $filename = 'facture_' . ($facture->reference ?? $facture->id) . '.pdf';
        $existing = Document::where('facture_id', $facture->id)
            ->where('type_document', 'Facture')
            ->first();

        $path = $existing?->chemin ?? 'documents/' . uniqid('facture_') . '.pdf';
        Storage::disk('public')->put($path, $pdfBinary);

        return Document::updateOrCreate(
            ['facture_id' => $facture->id, 'type_document' => 'Facture'],
            [
                'user_id' => $facture->commercial_id,
                'client_id' => $facture->client_id,
                'nom_original' => $filename,
                'chemin' => $path,
                'type_mime' => 'application/pdf',
            ]
        );
    }
}
