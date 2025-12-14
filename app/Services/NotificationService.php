<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function notifyCourseAvailable(User $user, $course)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'course',
            'title' => 'New Course Available',
            'message' => "{$course->name} is now available",
            'icon' => '📚',
            'data' => ['course_id' => $course->id],
        ]);
    }

    public static function notifyLeaderboardRankChange(User $user, $oldRank, $newRank)
    {
        $change = $oldRank - $newRank;
        $direction = $change > 0 ? 'up' : 'down';
        $positions = abs($change);
        $message = "You moved $direction " . $positions . " position" . ($positions > 1 ? 's' : '');
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'leaderboard',
            'title' => 'Rank Changed',
            'message' => $message,
            'icon' => $change > 0 ? '📈' : '📉',
            'data' => ['old_rank' => $oldRank, 'new_rank' => $newRank],
        ]);
    }

    public static function notifyAchievementUnlocked(User $user, $achievement)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'achievement',
            'title' => 'Achievement Unlocked',
            'message' => "You unlocked '{$achievement->name}' badge",
            'icon' => '⭐',
            'data' => ['achievement_id' => $achievement->id],
        ]);
    }

    public static function notifyRewardEarned(User $user, $reward)
    {
        $rewardName = is_array($reward) ? $reward['name'] : $reward->name;
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'reward',
            'title' => 'Reward Earned',
            'message' => "You earned {$rewardName}",
            'icon' => '🎁',
            'data' => is_array($reward) ? $reward : ['reward_id' => $reward->id],
        ]);
    }

    public static function notifyCommunityActivity(User $user, $activity)
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'community',
            'title' => 'Community Activity',
            'message' => $activity['message'] ?? 'New activity in the community',
            'icon' => $activity['icon'] ?? '💬',
            'data' => $activity['data'] ?? [],
        ]);
    }

    public static function notifyAssignmentPosted(User $user, $assignment)
    {
        $assignmentTitle = is_array($assignment) ? $assignment['title'] : $assignment->title;
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'assignment',
            'title' => 'New Assignment Posted',
            'message' => "Assignment: {$assignmentTitle} is now available",
            'icon' => '📝',
            'data' => is_array($assignment) ? $assignment : ['assignment_id' => $assignment->id],
        ]);
    }

    public static function notifyLevelUp(User $user, $newLevel, $xpGained = null)
    {
        $message = "Congratulations! You reached level {$newLevel}";
        if ($xpGained) {
            $message .= " (+{$xpGained} XP)";
        }
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'achievement',
            'title' => 'Level Up!',
            'message' => $message,
            'icon' => '⚡',
            'data' => ['level' => $newLevel, 'xp_gained' => $xpGained],
        ]);
    }

    public static function notifyXPGained(User $user, $amount, $source = null)
    {
        $message = "You earned {$amount} XP";
        if ($source) {
            $message .= " from {$source}";
        }
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'achievement',
            'title' => 'XP Gained',
            'message' => $message,
            'icon' => '✨',
            'data' => ['amount' => $amount, 'source' => $source],
        ]);
    }

    public static function notifyStreakIncreased(User $user, $newStreak)
    {
        $message = "Amazing! You've maintained a {$newStreak} day streak";
        
        Notification::create([
            'user_id' => $user->id,
            'type' => 'achievement',
            'title' => 'Streak Increased!',
            'message' => $message,
            'icon' => '🔥',
            'data' => ['streak' => $newStreak],
        ]);
    }
}
