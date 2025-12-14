<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use App\Services\NotificationService;

class AchievementUnlocker
{
    /**
     * Unlock an achievement for a user and award XP.
     *
     * @param User $user
     * @param Achievement $achievement
     * @return bool True if achievement was unlocked, false if already unlocked
     */
    public function unlock(User $user, Achievement $achievement): bool
    {
        // Check if user already has this achievement
        if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            return false;
        }

        // Attach achievement with unlock timestamp
        $user->achievements()->attach($achievement->id, [
            'unlocked_at' => now(),
        ]);

        // Award XP
        $this->awardXP($user, $achievement);

        return true;
    }

    /**
     * Unlock multiple achievements for a user.
     *
     * @param User $user
     * @param array|iterable $achievements
     * @return array Array of [achievement_id => unlocked_bool]
     */
    public function unlockMultiple(User $user, $achievements): array
    {
        $results = [];

        foreach ($achievements as $achievement) {
            if ($achievement instanceof Achievement) {
                $results[$achievement->id] = $this->unlock($user, $achievement);
            }
        }

        return $results;
    }

    /**
     * Award XP from an achievement to a user.
     *
     * @param User $user
     * @param Achievement $achievement
     */
    private function awardXP(User $user, Achievement $achievement): void
    {
        if (!$achievement->xp_reward || $achievement->xp_reward <= 0) {
            return;
        }

        // Get or create user profile
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Beginner',
        ]);

        // Get old level before update
        $oldLevel = $profile->level;

        // Add the XP reward
        $newTotalXP = $profile->total_xp + $achievement->xp_reward;
        $newLevel = max(1, intval($newTotalXP / 100) + 1);

        // Update profile object in memory first to calculate correct rank title
        $profile->level = $newLevel;
        $newRankTitle = $profile->calculateRankTitle();

        // Update profile with new total XP and recalculate level
        $profile->update([
            'total_xp' => $newTotalXP,
            'level' => $newLevel,
            'current_level_xp' => $newTotalXP % 100,
            'rank_title' => $newRankTitle,
        ]);

        // Send notifications
        NotificationService::notifyXPGained($user, $achievement->xp_reward, $achievement->name);
        NotificationService::notifyAchievementUnlocked($user, $achievement);

        // Notify level up if level increased
        if ($newLevel > $oldLevel) {
            NotificationService::notifyLevelUp($user, $newLevel, $achievement->xp_reward);
        }
    }

    /**
     * Check if user has achievement and award XP if missing.
     * Useful for syncing XP when achievements already exist.
     *
     * @param User $user
     * @param Achievement $achievement
     */
    public function syncXPForUnlockedAchievement(User $user, Achievement $achievement): void
    {
        if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
            $this->awardXP($user, $achievement);
        }
    }

    /**
     * Sync all user's achievement XP.
     * Call this to recalculate total XP from all unlocked achievements.
     *
     * @param User $user
     */
    public function syncAllAchievementXP(User $user): void
    {
        // Get total XP from all unlocked achievements
        $totalAchievementXP = $user->achievements()
            ->wherePivot('unlocked_at', '!=', null)
            ->sum('xp_reward');

        // Get or create user profile
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Beginner',
        ]);

        // Take the maximum of achievement XP vs current total (to preserve manually awarded points)
        $newTotalXP = max($totalAchievementXP, $profile->total_xp);
        $newLevel = max(1, intval($newTotalXP / 100) + 1);

        // Update profile object in memory first to calculate correct rank title
        $profile->level = $newLevel;
        $newRankTitle = $profile->calculateRankTitle();

        // Update profile
        $profile->update([
            'total_xp' => $newTotalXP,
            'level' => $newLevel,
            'current_level_xp' => $newTotalXP % 100,
            'rank_title' => $newRankTitle,
        ]);
    }
}
