<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Historique tracé des entrées/sorties de stock d'un Materiau.
 */
class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'materiau_id',
        'intervention_id',
        'technicien_id',
        'user_id',
        'type_mouvement',
        'quantite',
        'stock_apres',
        'commentaire',
    ];

    protected $casts = [
        'quantite'    => 'decimal:2',
        'stock_apres' => 'decimal:2',
    ];

    public function materiau()
    {
        return $this->belongsTo(Materiau::class);
    }

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
