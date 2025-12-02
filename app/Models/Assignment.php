<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'due_date',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
