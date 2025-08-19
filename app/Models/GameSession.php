<?php
// app/Models/GameSession.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameSession extends Model
{
    use HasFactory;

    protected $table = 'game_sessions';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'guild_id',
        'car_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'attempts_make',
        'attempts_model',
        'make_found',
        'model_found',
        'completed',
        'abandoned',
        'timeout',
        'car_changes_used',
        'hints_used',
        'points_earned',
        'difficulty_points_earned'
    ];

    protected $casts = [
        'user_id' => 'string',
        'guild_id' => 'string',
        'car_id' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'attempts_make' => 'integer',
        'attempts_model' => 'integer',
        'make_found' => 'boolean',
        'model_found' => 'boolean',
        'completed' => 'boolean',
        'abandoned' => 'boolean',
        'timeout' => 'boolean',
        'car_changes_used' => 'integer',
        'hints_used' => 'json',
        'points_earned' => 'decimal:2',
        'difficulty_points_earned' => 'decimal:2'
    ];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_id', 'id');
    }

    public function userScore()
    {
        return $this->belongsTo(UserScore::class, 'user_id', 'user_id');
    }

    public function getStatusAttribute()
    {
        if ($this->completed)
            return 'Terminé';
        if ($this->abandoned)
            return 'Abandonné';
        if ($this->timeout)
            return 'Timeout';
        return 'En cours';
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'Terminé' => 'bg-success',
            'Abandonné' => 'bg-danger',
            'Timeout' => 'bg-warning',
            'En cours' => 'bg-info'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration_seconds)
            return 'N/A';

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%dm %02ds', $minutes, $seconds);
    }

    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    public function scopeAbandoned($query)
    {
        return $query->where('abandoned', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('completed', false)
            ->where('abandoned', false)
            ->where('timeout', false);
    }

    public function scopeByGuild($query, $guildId)
    {
        return $query->where('guild_id', $guildId);
    }

    public static function getStats()
    {
        return [
            'total' => self::count(),
            'completed' => self::completed()->count(),
            'abandoned' => self::abandoned()->count(),
            'in_progress' => self::inProgress()->count(),
            'average_duration' => self::whereNotNull('duration_seconds')
                ->avg('duration_seconds'),
            'total_points_earned' => self::sum('points_earned')
        ];
    }
}