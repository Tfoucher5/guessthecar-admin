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

    public function getCountryFlagAttribute()
    {
        $flags = [
            'France' => '🇫🇷',
            'Germany' => '🇩🇪',
            'Italy' => '🇮🇹',
            'Japan' => '🇯🇵',
            'USA' => '🇺🇸',
            'UK' => '🇬🇧',
            'South Korea' => '🇰🇷',
            'Sweden' => '🇸🇪',
            'Czech Republic' => '🇨🇿',
            'Inconnu' => '🌍'
        ];

        return $flags[$this->country] ?? '🌍';
    }
}