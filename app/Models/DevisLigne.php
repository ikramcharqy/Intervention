<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevisLigne extends Model
{
    use HasFactory;

    protected $table = 'devis_lignes';

    protected $fillable = [

        'devis_id',

        'designation',

        'description',

        'quantite',

        'prix_unitaire',

        'montant_ht'

    ];

    protected $casts = [

        'quantite'=>'float',

        'prix_unitaire'=>'float',

        'montant_ht'=>'float'

    ];

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }
}