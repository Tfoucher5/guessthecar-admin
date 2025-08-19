<?php
// app/Models/UserCarFound.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCarFound extends Model
{
    use HasFactory;

    protected $table = 'user_cars_found';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'guild_id',
        'car_id',
        'brand_id',
        'found_at',
        'attempts_used',
        'time_taken'
    ];

    protected $casts = [
        'user_id' => 'string',
        'guild_id' => 'string',
        'car_id' => 'integer',
        'brand_id' => 'integer',
        'found_at' => 'datetime',
        'attempts_used' => 'integer',
        'time_taken' => 'integer'
    ];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function userScore()
    {
        return $this->belongsTo(UserScore::class, 'user_id', 'user_id');
    }

    public function getTimeTakenFormattedAttribute()
    {
        if (!$this->time_taken)
            return 'N/A';

        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;

        return sprintf('%dm %02ds', $minutes, $seconds);
    }

    public function scopeByGuild($query, $guildId)
    {
        return $query->where('guild_id', $guildId);
    }
}