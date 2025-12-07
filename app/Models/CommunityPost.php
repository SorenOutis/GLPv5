<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'excerpt',
        'views',
        'likes',
        'is_pinned',
        'is_approved',
        'status',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(CommunityPostReaction::class);
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function userReaction($userId)
    {
        return $this->reactions()->where('user_id', $userId)->first();
    }

    public function hasUserViewed($userId)
    {
        return $this->views()->where('user_id', $userId)->exists();
    }
}
