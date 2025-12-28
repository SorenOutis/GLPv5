<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameProgress extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'status',
        'progress',
        'time_spent_seconds',
        'rating',
        'player_count',
    ];

    protected $casts = [
        'progress' => 'integer',
        'time_spent_seconds' => 'integer',
        'rating' => 'float',
        'player_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function getTimeSpentFormattedAttribute(): string
    {
        $seconds = $this->time_spent_seconds ?? 0;
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return sprintf('%dh %dm', $hours, $minutes);
    }
}
