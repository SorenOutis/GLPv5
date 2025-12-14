<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\NotificationService;

class Streak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'longest_streak',
        'last_login_date',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'last_login_date' => 'date',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user associated with this streak
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update or create streak on user login
     * This checks if the user logged in today and increments streak accordingly
     */
    public static function updateStreakOnLogin(User $user)
    {
        $today = Carbon::now()->toDateString();
        
        $streak = self::where('user_id', $user->id)->first();
        
        if (!$streak) {
            // Create new streak record for new user
            $streak = self::create([
                'user_id' => $user->id,
                'current_streak' => 1,
                'longest_streak' => 1,
                'last_login_date' => $today,
                'last_login_at' => Carbon::now(),
            ]);
        } else {
            $lastLoginDate = $streak->last_login_date?->toDateString();
            
            if ($lastLoginDate === $today) {
                // User already logged in today, don't increment
                $streak->update([
                    'last_login_at' => Carbon::now(),
                ]);
            } else {
                $yesterday = Carbon::now()->subDay()->toDateString();
                
                if ($lastLoginDate === $yesterday) {
                    // User logged in yesterday, increment streak
                    $newStreak = $streak->current_streak + 1;
                    $newLongestStreak = max($streak->longest_streak, $newStreak);
                    
                    $streak->update([
                        'current_streak' => $newStreak,
                        'longest_streak' => $newLongestStreak,
                        'last_login_date' => $today,
                        'last_login_at' => Carbon::now(),
                    ]);

                    // Notify user about streak increase
                    NotificationService::notifyStreakIncreased($user, $newStreak);
                } else {
                    // Streak broken, reset to 1
                    $streak->update([
                        'current_streak' => 1,
                        'last_login_date' => $today,
                        'last_login_at' => Carbon::now(),
                    ]);
                }
            }
        }
        
        return $streak;
    }

    /**
     * Get current streak count
     */
    public function getCurrentStreak(): int
    {
        return $this->current_streak ?? 0;
    }

    /**
     * Get longest streak count
     */
    public function getLongestStreak(): int
    {
        return $this->longest_streak ?? 0;
    }

    /**
     * Check if user has logged in today
     */
    public function hasLoggedInToday(): bool
    {
        if (!$this->last_login_date) {
            return false;
        }
        
        return $this->last_login_date->toDateString() === Carbon::now()->toDateString();
    }

    /**
     * Reset streak (if manual reset needed from admin)
     */
    public function resetStreak()
    {
        $this->update([
            'current_streak' => 0,
            'last_login_date' => null,
            'last_login_at' => null,
        ]);
        
        return $this;
    }
}
