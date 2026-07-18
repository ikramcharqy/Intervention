<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Complète la table tracking_sessions (précédemment vide).
     * Chaque session représente un événement de présence du technicien
     * enregistré lors du démarrage d'une intervention (GPS, QR Code, NFC ou Manuel).
     */
    public function up(): void
    {
        Schema::table('tracking_sessions', function (Blueprint $table) {

            // Intervention concernée
            $table->foreignId('intervention_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->after('id');

            // Technicien qui a déclenché la session
            $table->foreignId('technicien_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->after('intervention_id');

            // Mode de présence utilisé
            $table->enum('mode', ['GPS', 'QR', 'NFC', 'Manuel'])
                  ->after('technicien_id');

            // Coordonnées GPS du technicien au moment de la session (mode GPS)
            $table->decimal('latitude', 10, 7)
                  ->nullable()
                  ->after('mode');

            $table->decimal('longitude', 10, 7)
                  ->nullable()
                  ->after('latitude');

            // Code QR scanné (mode QR)
            $table->string('qr_code_scan')
                  ->nullable()
                  ->after('longitude');

            // UID NFC scanné (mode NFC)
            $table->string('nfc_uid_scan')
                  ->nullable()
                  ->after('qr_code_scan');

            // Horodatage du début de la session
            $table->dateTime('started_at')
                  ->after('nfc_uid_scan');

            // Horodatage de fin de session (nullable = session toujours active)
            $table->dateTime('ended_at')
                  ->nullable()
                  ->after('started_at');
        });
    }

    /**
     * Annule les modifications.
     */
    public function down(): void
    {
        Schema::table('tracking_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('intervention_id');
            $table->dropConstrainedForeignId('technicien_id');
            $table->dropColumn([
                'mode',
                'latitude',
                'longitude',
                'qr_code_scan',
                'nfc_uid_scan',
                'started_at',
                'ended_at',
            ]);
        });
    }
};
