<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyLoginBonus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyLoginBonusService
{
    const DAILY_BONUS_XP = 20;

    /**
     * Check if user has already received bonus today
     * Uses explicit date comparison to avoid timezone issues
     */
    public function hasReceivedBonusToday(User $user): bool
    {
        $today = now()->toDateString(); // Gets date in app timezone
        
        return DailyLoginBonus::where('user_id', $user->id)
            ->where(DB::raw("DATE(bonus_date)"), '=', $today)
            ->exists();
    }

    /**
     * Get the last bonus date for a user
     */
    public function getLastBonusDate(User $user): ?Carbon
    {
        $bonus = DailyLoginBonus::where('user_id', $user->id)
            ->latest('bonus_date')
            ->first();
            
        return $bonus ? $bonus->bonus_date : null;
    }

    /**
     * Award daily login bonus to user
     * Uses database transaction to ensure atomicity
     */
    public function awardDailyBonus(User $user): array
    {
        return DB::transaction(function () use ($user) {
            // Double-check: prevent race conditions
            if ($this->hasReceivedBonusToday($user)) {
                return [
                    'success' => false,
                    'message' => 'You have already claimed your bonus today. Come back tomorrow!',
                    'xp_awarded' => 0,
                ];
            }

            try {
                // Get or create user profile
                $profile = $user->profile()->firstOrCreate([], [
                    'total_xp' => 0,
                    'level' => 1,
                    'current_level_xp' => 0,
                    'xp_for_next_level' => 1000,
                    'streak_days' => 0,
                    'rank_title' => 'Plastic',
                ]);

                // Award XP
                $profile->total_xp += self::DAILY_BONUS_XP;
                $profile->current_level_xp += self::DAILY_BONUS_XP;

                // Record the bonus with today's date
                DailyLoginBonus::create([
                    'user_id' => $user->id,
                    'bonus_date' => now()->toDateString(), // Store just the date
                    'xp_awarded' => self::DAILY_BONUS_XP,
                ]);

                $profile->save();

                return [
                    'success' => true,
                    'message' => 'Daily bonus awarded!',
                    'xp_awarded' => self::DAILY_BONUS_XP,
                    'total_xp' => $profile->total_xp,
                    'level' => $profile->level,
                    'current_xp' => $profile->current_level_xp,
                ];
            } catch (\Illuminate\Database\QueryException $e) {
                // Catch duplicate entry errors
                if ($e->getCode() == 23000) {
                    return [
                        'success' => false,
                        'message' => 'You have already claimed your bonus today. Come back tomorrow!',
                        'xp_awarded' => 0,
                    ];
                }
                throw $e;
            }
        });
    }
}
