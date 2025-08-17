<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    use HasFactory;

    protected $table = 'game_sessions';

    // Pas de timestamps automatiques car on utilise started_at/ended_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
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

    /**
     * Relation avec le modèle de voiture (car_id = model_id)
     */
    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_id', 'id');
    }

    /**
     * Relation avec le joueur (user_scores)
     */
    public function userScore()
    {
        return $this->belongsTo(UserScore::class, 'user_id', 'user_id');
    }

    /**
     * Scope pour les sessions complétées
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope pour les sessions abandonnées
     */
    public function scopeAbandoned($query)
    {
        return $query->where('abandoned', true);
    }

    /**
     * Scope pour les sessions en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('completed', false)
            ->where('abandoned', false)
            ->where('timeout', false);
    }

    /**
     * Scope pour les sessions récentes (24h)
     */
    public function scopeRecent($query)
    {
        return $query->where('started_at', '>=', now()->subDay());
    }

    /**
     * Attribut calculé : statut de la session
     */
    public function getStatusAttribute()
    {
        if ($this->completed) {
            return 'completed';
        }
        if ($this->abandoned) {
            return 'abandoned';
        }
        if ($this->timeout) {
            return 'timeout';
        }
        return 'in_progress';
    }

    /**
     * Attribut calculé : statut en français
     */
    public function getStatusFrenchAttribute()
    {
        return match ($this->status) {
            'completed' => 'Terminée',
            'abandoned' => 'Abandonnée',
            'timeout' => 'Expirée',
            'in_progress' => 'En cours',
            default => 'Inconnue'
        };
    }

    /**
     * Attribut calculé : couleur du statut
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'completed' => 'green',
            'abandoned' => 'red',
            'timeout' => 'yellow',
            'in_progress' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Attribut calculé : durée formatée
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Attribut calculé : total des tentatives
     */
    public function getTotalAttemptsAttribute()
    {
        return $this->attempts_make + $this->attempts_model;
    }

    /**
     * Attribut calculé : total des points
     */
    public function getTotalPointsAttribute()
    {
        return $this->points_earned + $this->difficulty_points_earned;
    }

    /**
     * Méthode statique : statistiques générales
     */
    public static function getStats()
    {
        $total = self::count();

        return [
            'total_sessions' => $total,
            'completed_sessions' => self::completed()->count(),
            'abandoned_sessions' => self::abandoned()->count(),
            'in_progress_sessions' => self::inProgress()->count(),
            'total_points_earned' => self::sum('points_earned'),
            'total_difficulty_points' => self::sum('difficulty_points_earned'),
            'average_duration' => self::whereNotNull('duration_seconds')->avg('duration_seconds'),
            'success_rate' => $total > 0 ? (self::completed()->count() / $total * 100) : 0,
        ];
    }

    /**
     * Méthode statique : sessions récentes avec détails
     */
    public static function getRecentWithDetails($limit = 10)
    {
        return self::with(['carModel.brand', 'userScore'])
            ->recent()
            ->orderBy('started_at', 'desc')
            ->limit($limit)
            ->get();
    }
}