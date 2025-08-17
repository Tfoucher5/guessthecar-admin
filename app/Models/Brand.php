<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function models()
    {
        return $this->hasMany(CarModel::class);
    }

    public function gameSessions()
    {
        return $this->hasManyThrough(GameSession::class, CarModel::class, 'brand_id', 'car_id');
    }

    public function scopeWithModelCount($query)
    {
        return $query->withCount('models');
    }
}