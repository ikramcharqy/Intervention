<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ClientParticulier extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'numero_cin', 'date_naissance'];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Le CIN est une donnée personnelle sensible (loi 09-08) : chiffré au repos
     * via Crypt (clé APP_KEY). Le chiffrement Laravel produit un IV aléatoire à
     * chaque appel, donc le ciphertext seul ne peut pas porter de contrainte
     * unique/recherche fiable : numero_cin_hash (SHA-256 déterministe du CIN
     * normalisé) sert d'index à cet effet, en parallèle du champ chiffré.
     */
    public function setNumeroCinAttribute(?string $value): void
    {
        $normalized = $value ? strtoupper(trim($value)) : null;

        $this->attributes['numero_cin'] = $normalized ? Crypt::encryptString($normalized) : null;
        $this->attributes['numero_cin_hash'] = $normalized ? hash('sha256', $normalized) : null;
    }

    public function getNumeroCinAttribute(?string $value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public static function hashCin(string $value): string
    {
        return hash('sha256', strtoupper(trim($value)));
    }
}
