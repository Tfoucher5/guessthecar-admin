<?php

// app/Models/Brand.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'logo_url'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function models()
    {
        return $this->hasMany(CarModel::class);
    }

    public function gameSessions()
    {
        return $this->hasManyThrough(GameSession::class, CarModel::class, 'brand_id', 'car_id');
    }

    public function userCarsFound()
    {
        return $this->hasMany(UserCarFound::class);
    }

    public function scopeWithModelCount($query)
    {
        return $query->withCount('models');
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Obtenir l'emoji drapeau du pays
     */
    public function getCountryFlagAttribute()
    {
        $flags = [
            'France' => '🇫🇷',
            'Allemagne' => '🇩🇪',
            'Italie' => '🇮🇹',
            'Espagne' => '🇪🇸',
            'Royaume-Uni' => '🇬🇧',
            'États-Unis' => '🇺🇸',
            'Japon' => '🇯🇵',
            'Corée du Sud' => '🇰🇷',
            'Chine' => '🇨🇳',
            'Suède' => '🇸🇪',
            'Norvège' => '🇳🇴',
            'Pays-Bas' => '🇳🇱',
            'Belgique' => '🇧🇪',
            'Suisse' => '🇨🇭',
            'Autriche' => '🇦🇹',
            'République tchèque' => '🇨🇿',
            'Pologne' => '🇵🇱',
            'Russie' => '🇷🇺',
            'Inde' => '🇮🇳',
            'Brésil' => '🇧🇷',
            'Canada' => '🇨🇦',
            'Australie' => '🇦🇺',
            'Roumanie' => '🇷🇴',
            'Malaisie' => '🇲🇾',
        ];

        return $flags[$this->country] ?? '🌍';
    }

    // Puis dans les vues, vous pourrez utiliser :
// {{ $brand->country_flag }}
}