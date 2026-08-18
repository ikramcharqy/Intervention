<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The settings table already exists (key/value store).
        // We just seed the company defaults here.
        $defaults = [
            'company_name'      => 'TechInterv Solutions',
            'company_tagline'   => 'Excellence en Maintenance & Interventions Techniques',
            'company_address'   => '12, Boulevard Hassan II – Casablanca, Maroc 20250',
            'company_phone'     => '+212 5 22 XX XX XX',
            'company_fax'       => '+212 5 22 XX XX XY',
            'company_email'     => 'contact@techinterv.ma',
            'company_website'   => 'www.techinterv.ma',
            'company_ice'       => '002XXXXXXXXX',
            'company_rc'        => 'RC XXXXXXX – Casablanca',
            'company_logo'      => '',  // path in storage/public or URL
            'company_color'     => '#4338CA',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insertOrIgnore([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'company_name', 'company_tagline', 'company_address',
            'company_phone', 'company_fax', 'company_email',
            'company_website', 'company_ice', 'company_rc',
            'company_logo', 'company_color',
        ])->delete();
    }
};
