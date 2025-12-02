<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'xp_reward',
        'difficulty_level',
        'is_active',
        'thumbnail_url',
        'total_lessons',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}
