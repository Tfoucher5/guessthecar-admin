<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'total_points',
        'difficulty_points',
        'games_played',
        'games_won',
        'correct_brand_guesses',
        'correct_model_guesses',
        'total_brand_guesses',
        'total_model_guesses',
        'best_streak',
        'current_streak',
        'best_time',
        'average_response_time'
    ];

    protected $casts = [
        'total_points' => 'decimal:2',
        'difficulty_points' => 'decimal:2',
        'games_played' => 'integer',
        'games_won' => 'integer',
        'correct_brand_guesses' => 'integer',
        'correct_model_guesses' => 'integer',
        'total_brand_guesses' => 'integer',
        'total_model_guesses' => 'integer',
        'best_streak' => 'integer',
        'current_streak' => 'integer',
        'best_time' => 'integer',
        'average_response_time' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class, 'user_id', 'user_id');
    }

    public function getWinRateAttribute()
    {
        return $this->games_played > 0
            ? round(($this->games_won / $this->games_played) * 100, 2)
            : 0;
    }

    public function getAverageTimeAttribute()
    {
        return $this->games_played > 0 && $this->best_time
            ? round($this->best_time / $this->games_played, 2)
            : 0;
    }

    public function getTotalPointsAllAttribute()
    {
        return $this->total_points + $this->difficulty_points;
    }

    public function scopeTopPlayers($query, $limit = 10)
    {
        return $query->orderBy('total_points', 'desc')->limit($limit);
    }
}