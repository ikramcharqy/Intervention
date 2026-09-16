<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public const CATEGORY_SECURITY = 'security';
    public const CATEGORY_BUSINESS = 'business';

    /** Modules métier — routés vers le "Journal d'Activité Métier" plutôt que Sécurité/Gouvernance. */
    private const BUSINESS_MODULES = ['Interventions', 'Commercial', 'Devis', 'Prospects'];

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'severity',
        'category',
        'ip_address',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function categoryForModule(string $module): string
    {
        return in_array($module, self::BUSINESS_MODULES, true)
            ? self::CATEGORY_BUSINESS
            : self::CATEGORY_SECURITY;
    }

    public function scopeSecurity(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_SECURITY);
    }

    public function scopeBusiness(Builder $query): Builder
    {
        return $query->where('category', self::CATEGORY_BUSINESS);
    }
}
