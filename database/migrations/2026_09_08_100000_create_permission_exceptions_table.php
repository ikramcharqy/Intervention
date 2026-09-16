<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Étape 2/3 du prompt "Interface d'assignation Rôles/Permissions" : trace structurée
 * des permissions individuelles (exceptions au modèle RBAC standard rôle→permissions),
 * distincte du pivot Spatie `model_has_permissions` (qui ne porte ni justification ni
 * historique) — permet l'audit consolidé de l'Étape 3 sans parser le Journal de Sécurité.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->text('justification');
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_exceptions');
    }
};
