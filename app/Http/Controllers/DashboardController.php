<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get or create user profile
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 500,
            'streak_days' => 0,
            'rank_title' => 'Beginner',
        ]);

        // Get active courses with enrollment progress
        $courses = $user->enrollments()
            ->with('course')
            ->get()
            ->map(fn ($enrollment) => [
                'id' => $enrollment->course->id,
                'name' => $enrollment->course->title,
                'progress' => $enrollment->progress_percentage,
                'completedLessons' => $enrollment->completed_lessons_count,
                'totalLessons' => $enrollment->course->total_lessons,
                'xpEarned' => $enrollment->xp_earned,
                'nextDeadline' => $enrollment->course->updated_at->format('Y-m-d'),
            ]);

        // Get leaderboard (top 5 users by XP)
        $leaderboard = \App\Models\User::with('profile')
            ->get()
            ->map(fn ($u) => [
                'name' => $u->name,
                'xp' => $u->profile?->total_xp ?? 0,
                'level' => $u->profile?->level ?? 1,
                'badge' => $this->getLevelBadge($u->profile?->level ?? 1),
                'isUser' => $u->id === $user->id,
            ])
            ->sortByDesc('xp')
            ->take(5)
            ->values()
            ->map(fn ($item, $index) => array_merge($item, ['rank' => $index + 1]));

        // Get unlocked achievements
        $achievements = $user->achievements()
            ->get()
            ->map(fn ($achievement) => [
                'id' => $achievement->id,
                'name' => $achievement->title,
                'description' => $achievement->description,
                'unlocked' => true,
                'icon' => $achievement->icon,
            ]);

        // Get locked achievements (available but not unlocked)
        $allAchievements = \App\Models\Achievement::where('is_active', true)
            ->get()
            ->map(fn ($achievement) => [
                'id' => $achievement->id,
                'name' => $achievement->title,
                'description' => $achievement->description,
                'unlocked' => $user->achievements->pluck('id')->contains($achievement->id),
                'icon' => $achievement->icon,
            ]);

        // Get recent activity
        $recentActivity = $user->lessonCompletions()
            ->with('lesson')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($completion) => [
                'type' => 'lesson',
                'title' => 'Completed: ' . $completion->lesson->title,
                'xp' => $completion->lesson->xp_reward,
                'timestamp' => $completion->completed_at->diffForHumans(),
            ]);

        return Inertia::render('Dashboard', [
            'userStats' => [
                'totalXP' => $profile->total_xp,
                'level' => $profile->level,
                'currentXP' => $profile->current_level_xp,
                'maxXPForLevel' => $profile->xp_for_next_level,
                'rank' => $profile->rank_title,
                'streakDays' => $profile->streak_days,
                'achievements' => $achievements->count(),
            ],
            'courses' => $courses,
            'leaderboard' => $leaderboard,
            'achievements' => $allAchievements,
            'recentActivity' => $recentActivity,
        ]);
    }

    private function getLevelBadge(int $level): string
    {
        return match (true) {
            $level >= 15 => '⭐',
            $level >= 12 => '🔥',
            $level >= 10 => '🚀',
            $level >= 5 => '💪',
            default => '⚡',
        };
    }
}
