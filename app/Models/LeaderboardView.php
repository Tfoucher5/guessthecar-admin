<?php
// app/Models/LeaderboardView.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardView extends Model
{
    protected $table = 'leaderboard_view';
    public $timestamps = false;

    protected $casts = [
        'user_id' => 'string',
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
        'ranking' => 'integer',
        'success_rate' => 'decimal:1',
        'average_attempts' => 'decimal:1',
        'average_time_seconds' => 'decimal:1'
    ];

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
}