<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Service de validation sécurisée des fichiers uploadés.
 *
 * Vérifie le vrai MIME type (via finfo), l'extension autorisée,
 * et la taille maximale avant tout stockage.
 */
class SecureFileUploadService
{
    // Mappage MIME → extensions autorisées
    private const ALLOWED = [
        'image' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
            'ext'  => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
            'max'  => 10 * 1024 * 1024, // 10 Mo
        ],
        'signature' => [
            'mime' => ['image/jpeg', 'image/png', 'image/webp'],
            'ext'  => ['jpg', 'jpeg', 'png', 'webp'],
            'max'  => 5 * 1024 * 1024, // 5 Mo
        ],
        'video' => [
            'mime' => ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/webm'],
            'ext'  => ['mp4', 'mov', 'avi', 'webm'],
            'max'  => 100 * 1024 * 1024, // 100 Mo
        ],
        'document' => [
            'mime' => [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ],
            'ext'  => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
            'max'  => 20 * 1024 * 1024, // 20 Mo
        ],
    ];

    /**
     * Valide un fichier uploadé selon un type attendu.
     *
     * @param  UploadedFile  $file      Le fichier soumis
     * @param  string        $type      'image', 'video', 'document', 'signature'
     * @param  int|null      $userId    Pour logging (optionnel)
     * @throws \InvalidArgumentException si le fichier ne passe pas la validation
     */
    public function validate(UploadedFile $file, string $type, ?int $userId = null): void
    {
        $rules = self::ALLOWED[$type] ?? null;

        if (!$rules) {
            throw new \InvalidArgumentException("Type de fichier inconnu : {$type}");
        }

        // 1. Vérifier la taille
        if ($file->getSize() > $rules['max']) {
            $maxMo = $rules['max'] / (1024 * 1024);
            throw new \InvalidArgumentException("Le fichier dépasse la taille maximale autorisée ({$maxMo} Mo).");
        }

        // 2. Vérifier le vrai MIME type via finfo (contenu binaire, pas juste l'en-tête HTTP)
        $realMime = $this->getRealMimeType($file->getRealPath());

        if (!in_array($realMime, $rules['mime'], true)) {
            Log::warning('Upload refusé — MIME non autorisé', [
                'user_id'      => $userId,
                'expected_type' => $type,
                'real_mime'    => $realMime,
                'filename'     => $file->getClientOriginalName(),
            ]);
            throw new \InvalidArgumentException(
                "Type de fichier non autorisé. MIME détecté : {$realMime}"
            );
        }

        // 3. Vérifier l'extension déclarée par le client (protection complémentaire)
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $rules['ext'], true)) {
            throw new \InvalidArgumentException(
                "Extension de fichier non autorisée : .{$ext}"
            );
        }
    }

    /**
     * Valide un batch de fichiers uploadés (tableau de UploadedFile).
     */
    public function validateBatch(array $files, string $type, ?int $userId = null): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $this->validate($file, $type, $userId);
            }
        }
    }

    /**
     * Récupère le vrai MIME type d'un fichier via finfo (lecture binaire).
     */
    private function getRealMimeType(string $filePath): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            return $mime ?: 'application/octet-stream';
        }

        // Fallback : mime_content_type si finfo non disponible
        return mime_content_type($filePath) ?: 'application/octet-stream';
    }
}
