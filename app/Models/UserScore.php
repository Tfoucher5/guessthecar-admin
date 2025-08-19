<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guild_id',
        'username',
        'total_points',
        'total_difficulty_points',
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
        'user_id' => 'string',
        'guild_id' => 'string',
        'total_points' => 'decimal:2',
        'total_difficulty_points' => 'decimal:2',
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

    public function userCarsFound()
    {
        return $this->hasMany(UserCarFound::class, 'user_id', 'user_id');
    }

    public function getSuccessRateAttribute()
    {
        return $this->games_played > 0
            ? round(($this->games_won / $this->games_played) * 100, 1)
            : 0;
    }

    public function getBrandAccuracyAttribute()
    {
        return $this->total_brand_guesses > 0
            ? round(($this->correct_brand_guesses / $this->total_brand_guesses) * 100, 1)
            : 0;
    }

    public function getModelAccuracyAttribute()
    {
        return $this->total_model_guesses > 0
            ? round(($this->correct_model_guesses / $this->total_model_guesses) * 100, 1)
            : 0;
    }

    public function getSkillLevelAttribute()
    {
        if ($this->total_points >= 100)
            return 'Expert';
        if ($this->total_points >= 50)
            return 'Avancé';
        if ($this->total_points >= 20)
            return 'Intermédiaire';
        if ($this->total_points >= 10)
            return 'Apprenti';
        return 'Débutant';
    }

    public function getSkillBadgeClassAttribute()
    {
        $classes = [
            'Expert' => 'bg-primary',
            'Avancé' => 'bg-success',
            'Intermédiaire' => 'bg-warning',
            'Apprenti' => 'bg-info',
            'Débutant' => 'bg-secondary'
        ];

        return $classes[$this->skill_level] ?? 'bg-secondary';
    }

    public function scopeTopPlayers($query, $limit = 10)
    {
        return $query->orderBy('total_points', 'desc')
            ->orderBy('games_won', 'desc')
            ->limit($limit);
    }

    public function scopeByGuild($query, $guildId)
    {
        return $query->where('guild_id', $guildId);
    }
}