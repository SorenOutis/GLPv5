<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Streak;
use Illuminate\Http\JsonResponse;

class StreakLeaderboardController extends Controller
{
    /**
     * Get streak leaderboard data
     * Returns users sorted by current streak and longest streak
     */
    public function index(): JsonResponse
    {
        // Get all streaks with their associated users, excluding admin/staff
        $streaks = Streak::with('user')
            ->get()
            ->filter(function ($streak) {
                // Exclude admin, staff, teacher, and super_admin roles
                return !$streak->user->hasAnyRole(['admin', 'staff', 'teacher', 'super_admin']);
            })
            ->map(function ($streak) {
                $profilePhoto = $streak->user->profile_photo_path 
                    ? asset('storage/' . $streak->user->profile_photo_path)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($streak->user->name) . '&background=random';
                
                return [
                    'id' => $streak->user->id,
                    'name' => $streak->user->name,
                    'email' => $streak->user->email,
                    'currentStreak' => $streak->current_streak ?? 0,
                    'longestStreak' => $streak->longest_streak ?? 0,
                    'lastLoginDate' => $streak->last_login_date?->format('Y-m-d'),
                    'profilePhoto' => $profilePhoto,
                ];
            });

        // Current streak sorted A-Z by name
        $currentStreakSorted = $streaks->sortBy('name')
            ->values()
            ->all();

        // Longest streak sorted by highest streak descending
        $longestStreakSorted = $streaks->sortByDesc('longestStreak')
            ->values()
            ->all();

        return response()->json([
            'currentStreak' => $currentStreakSorted,
            'longestStreak' => $longestStreakSorted,
        ]);
    }
}
