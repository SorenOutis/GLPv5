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
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Plastic',
        ]);

        // Get streak information from new Streak model
        $streak = $user->streak()->firstOrCreate([], [
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_login_date' => null,
            'last_login_at' => null,
        ]);

        // Check if user has received daily bonus
        $bonusService = new \App\Services\DailyLoginBonusService();
        $hasReceivedBonus = $bonusService->hasReceivedBonusToday($user);

        // Get total XP from profile (includes both assignment XP and manually awarded points)
        $totalXP = $profile->total_xp;
        $level = $profile->level;
        $currentLevelXP = $profile->current_level_xp;

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

        // Get leaderboard (top 5 users by total XP from database)
         // Exclude admin users - only show students
         $leaderboard = \App\Models\User::with('profile')
             ->where('id', '>', 1) // Exclude first user (admin)
             ->get()
             ->filter(function ($u) {
                 // Also exclude users with admin-related roles
                 return !$u->hasAnyRole(['admin', 'staff', 'teacher', 'super_admin']);
             })
             ->map(fn ($u) => [
                 'id' => $u->id,
                 'name' => $u->name,
                 'xp' => $u->profile?->total_xp ?? 0,
                 'level' => $u->profile?->level ?? 1,
                 'isUser' => $u->id === $user->id,
             ])
             ->map(fn ($item) => [
                 'id' => $item['id'],
                 'name' => $item['name'],
                 'xp' => $item['xp'],
                 'level' => $item['level'],
                 'badge' => $this->getLevelBadge($item['level']),
                 'isUser' => $item['isUser'],
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

        // Get pending assignments (not submitted or not graded)
        $assignments = \App\Models\Assignment::where('is_active', true)
            ->with('submissions')
            ->get()
            ->map(function ($assignment) use ($user) {
                $userSubmission = $assignment->submissions()
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'dueDate' => $assignment->due_date?->format('Y-m-d'),
                    'isOverdue' => $assignment->due_date && $assignment->due_date->isPast(),
                    'submitted' => $userSubmission ? true : false,
                    'status' => $userSubmission?->status ?? 'pending',
                    'grade' => $userSubmission?->grade,
                ];
            })
            ->sortBy(fn ($a) => $a['isOverdue'] ? 0 : 1)
            ->values();

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
            'userName' => $user->name,
            'userStats' => [
                'totalXP' => $totalXP,
                'level' => $level,
                'currentXP' => $currentLevelXP,
                'maxXPForLevel' => 100,
                'rank' => $profile->rank_title,
                'streakDays' => $profile->streak_days,
                'achievements' => $achievements->count(),
            ],
            'streak' => [
                'currentStreak' => $streak->current_streak,
                'longestStreak' => $streak->longest_streak,
                'lastLoginDate' => $streak->last_login_date,
            ],
            'dailyBonus' => [
                'hasReceivedToday' => $hasReceivedBonus,
                'xpAmount' => 20,
            ],
            'courses' => $courses,
            'assignments' => $assignments,
            'leaderboard' => $leaderboard,
            'achievements' => $allAchievements,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function getStats()
    {
        $user = auth()->user();

        // Get or create user profile
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Plastic',
        ]);

        $totalXP = $profile->total_xp;
        $level = $profile->level;
        $currentLevelXP = $profile->current_level_xp;

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

        // Get leaderboard (top 5 users by total XP from database)
        $leaderboard = \App\Models\User::with('profile')
            ->where('id', '>', 1) // Exclude first user (admin)
            ->get()
            ->filter(function ($u) {
                // Also exclude users with admin-related roles
                return !$u->hasAnyRole(['admin', 'staff', 'teacher', 'super_admin']);
            })
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'xp' => $u->profile?->total_xp ?? 0,
                'level' => $u->profile?->level ?? 1,
                'isUser' => $u->id === $user->id,
            ])
            ->map(fn ($item) => [
                'id' => $item['id'],
                'name' => $item['name'],
                'xp' => $item['xp'],
                'level' => $item['level'],
                'badge' => $this->getLevelBadge($item['level']),
                'isUser' => $item['isUser'],
            ])
            ->sortByDesc('xp')
            ->take(5)
            ->values()
            ->map(fn ($item, $index) => array_merge($item, ['rank' => $index + 1]));

        // Get pending assignments (not submitted or not graded)
        $assignments = \App\Models\Assignment::where('is_active', true)
            ->with('submissions')
            ->get()
            ->map(function ($assignment) use ($user) {
                $userSubmission = $assignment->submissions()
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'description' => $assignment->description,
                    'dueDate' => $assignment->due_date?->format('Y-m-d'),
                    'isOverdue' => $assignment->due_date && $assignment->due_date->isPast(),
                    'submitted' => $userSubmission ? true : false,
                    'status' => $userSubmission?->status ?? 'pending',
                    'grade' => $userSubmission?->grade,
                ];
            })
            ->sortBy(fn ($a) => $a['isOverdue'] ? 0 : 1)
            ->values();

        return response()->json([
            'userStats' => [
                'totalXP' => $totalXP,
                'level' => $level,
                'currentXP' => $currentLevelXP,
                'maxXPForLevel' => 100,
                'rank' => $profile->rank_title,
                'streakDays' => $profile->streak_days,
                'achievements' => $user->achievements()->count(),
            ],
            'courses' => $courses,
            'assignments' => $assignments,
            'leaderboard' => $leaderboard,
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
