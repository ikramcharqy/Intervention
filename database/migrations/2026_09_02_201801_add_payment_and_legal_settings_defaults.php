<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'payment_mode' => 'Virement bancaire, chèque ou espèces',
            'payment_delay_days' => '30',
            'payment_deposit_percent' => '0',
            'legal_mentions' => "Devis valable jusqu'à la date d'expiration indiquée ci-dessus. Tout retard de paiement au-delà du délai convenu entraîne l'application de pénalités de retard au taux légal en vigueur, sans mise en demeure préalable. Conditions générales de vente disponibles sur simple demande.",
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insertOrIgnore([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'payment_mode', 'payment_delay_days', 'payment_deposit_percent', 'legal_mentions',
        ])->delete();
    }
};
