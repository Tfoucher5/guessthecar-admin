<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'brand_id',
        'name',
        'year',
        'difficulty_level',
        'image_url'
    ];

    protected $casts = [
        'brand_id' => 'integer',
        'year' => 'integer',
        'difficulty_level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class, 'car_id');
    }

    public function userCarsFound()
    {
        return $this->hasMany(UserCarFound::class, 'car_id');
    }

    public function getDifficultyTextAttribute()
    {
        $difficulties = [
            1 => 'Facile',
            2 => 'Moyen',
            3 => 'Difficile'
        ];

        return $difficulties[$this->difficulty_level] ?? 'Inconnu';
    }

    public function getDifficultyBadgeClassAttribute()
    {
        $classes = [
            1 => 'bg-success',
            2 => 'bg-warning',
            3 => 'bg-danger'
        ];

        return $classes[$this->difficulty_level] ?? 'bg-secondary';
    }

    public function getFullNameAttribute()
    {
        return $this->brand->name . ' ' . $this->name;
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeByYear($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('year', '>=', $from);
        }
        if ($to) {
            $query->where('year', '<=', $to);
        }
        return $query;
    }
}