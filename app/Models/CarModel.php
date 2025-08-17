<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'name',
        'brand_id',
        'year',
        'difficulty_level',
        'image_url'
    ];

    protected $casts = [
        'brand_id' => 'integer',
        'year' => 'integer',
        'difficulty_level' => 'integer',
        'created_at' => 'datetime'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class, 'car_id');
    }

    public function getDifficultyTextAttribute()
    {
        return match ($this->difficulty_level) {
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile',
            default => 'Inconnu'
        };
    }

    public function getDifficultyColorAttribute()
    {
        return match ($this->difficulty_level) {
            1 => 'green',
            2 => 'yellow',
            3 => 'red',
            default => 'gray'
        };
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }
}