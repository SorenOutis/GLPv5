<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'difficulty',
        'thumbnail',
        'badge',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userProgresses(): HasMany
    {
        return $this->hasMany(GameProgress::class);
    }
}
