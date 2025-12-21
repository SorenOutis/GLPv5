<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingChallenge extends Model
{
    protected $table = 'coding_challenges';

    protected $fillable = [
        'title',
        'description',
        'problem_statement',
        'requirements',
        'example_input',
        'example_output',
        'tips',
        'difficulty',
        'language',
        'category',
        'xp_reward',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'requirements' => 'array',
        'tips' => 'array',
    ];
}
