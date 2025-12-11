<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLoginBonus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bonus_date',
        'xp_awarded',
        'claimed_at',
    ];

    protected $casts = [
        'bonus_date' => 'date',
        'claimed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
