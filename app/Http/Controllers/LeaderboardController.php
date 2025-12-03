<?php

namespace App\Http\Controllers;

use App\Models\RankingTier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $currentProfile = $currentUser->profile;

        // Get all users with their profiles and streaks, ordered by total XP (for leaderboard display)
        // Exclude admin and staff users - only show students
        $allUsers = User::with('profile', 'streak')
            ->whereHas('profile')
            ->where('id', '>', 1) // Exclude first user (admin)
            ->get()
            ->filter(function ($user) {
                // Additional exclusions: users with any admin-related roles
                return !$user->hasAnyRole(['admin', 'staff', 'teacher', 'super_admin']);
            })
            ->sortByDesc(fn($user) => $user->profile->total_xp)
            ->values();

        // Create leaderboard entries with pagination (20 per page initially)
        $leaderboardEntries = $allUsers->map(function ($user, $index) use ($currentUser) {
            $achievements = $user->achievements()->count();
            // Calculate rank tier based on the user's level, not leaderboard position
            // Map levels to rank positions: Level 1 = Rank 131+, Level 2 = Rank 101-130, etc.
            $rankTier = $this->getRankTierByLevel((int) $user->profile->level);
            
            // Get streak from new Streak model, fallback to profile streak_days
            $streakDays = $user->streak ? $user->streak->current_streak : (int) $user->profile->streak_days;
            
            return [
                'rank' => $index + 1,
                'userId' => $user->id,
                'name' => $user->name,
                'xp' => (int) $user->profile->total_xp,
                'level' => (int) $user->profile->level,
                'streakDays' => $streakDays,
                'achievements' => $achievements,
                'isCurrentUser' => $user->id === $currentUser->id,
                'rankTier' => $rankTier,
            ];
        })->toArray();

        // Get current user's streak
        $currentUserStreak = $currentUser->streak ? $currentUser->streak->current_streak : 0;
        
        // Get current user's rank
        $currentUserRank = collect($leaderboardEntries)
            ->where('userId', $currentUser->id)
            ->first() ?? [
            'rank' => count($allUsers) + 1,
            'userId' => $currentUser->id,
            'name' => $currentUser->name,
            'xp' => 0,
            'level' => 1,
            'streakDays' => $currentUserStreak,
            'achievements' => 0,
            'isCurrentUser' => true,
            'rankTier' => $this->getRankTierByLevel(1),
        ];

        $totalUsers = count($allUsers);
        $topXP = $allUsers->first()?->profile?->total_xp ?? 0;

        // Calculate XP to next level (example: 1000 XP per level)
        $xpPerLevel = 1000;
        $currentLevelXP = ($currentProfile->level - 1) * $xpPerLevel;
        $nextLevelXP = $currentProfile->level * $xpPerLevel;
        $xpToNextRank = max(0, $nextLevelXP - $currentProfile->total_xp);

        // Get all ranking tiers sorted by minRank
        $allTiers = RankingTier::orderBy('min_rank', 'asc')->get()->map(function ($tier) {
            return [
                'id' => $tier->id,
                'name' => $tier->name,
                'icon' => $tier->icon,
                'color' => $tier->color,
                'minRank' => $tier->min_rank,
                'maxRank' => $tier->max_rank,
            ];
        })->toArray();

        return Inertia::render('Leaderboard', [
            'leaderboard' => array_slice($leaderboardEntries, 0, 20),
            'leaderboardTotal' => count($leaderboardEntries),
            'userRank' => $currentUserRank,
            'allTiers' => $allTiers,
            'stats' => [
                'totalUsers' => $totalUsers,
                'topXP' => (int) $topXP,
                'myRank' => $currentUserRank['rank'],
                'xpToNextRank' => $xpToNextRank,
            ],
        ]);
    }

    /**
     * Get rank tier based on user level
     * Maps levels to tier rank positions
     */
    private function getRankTierByLevel(int $level): array
    {
        // Map levels to approximate rank positions
        // This converts level progression to tier progression
        $rankPosition = $this->levelToRankPosition($level);
        return $this->getRankTier($rankPosition);
    }

    /**
     * Convert user level to a rank position for tier matching
     * Level 1 = Plastic (131+)
     * Level 2 = Iron (101-130)
     * Level 3-4 = Bronze (81-100)
     * Level 5-6 = Silver (61-80)
     * Level 7-9 = Gold (46-60)
     * Level 10-11 = Platinum (36-45)
     * Level 12-14 = Diamond (26-35)
     * Level 15-19 = Apex (16-25)
     * Level 20-24 = Eternal (6-15)
     * Level 25+ = Supreme (1-5)
     */
    private function levelToRankPosition(int $level): int
    {
        return match (true) {
            $level >= 25 => 1,      // Supreme
            $level >= 20 => 6,      // Eternal
            $level >= 15 => 16,     // Apex
            $level >= 12 => 26,     // Diamond
            $level >= 10 => 36,     // Platinum
            $level >= 7 => 46,      // Gold
            $level >= 5 => 61,      // Silver
            $level >= 3 => 81,      // Bronze
            $level >= 2 => 101,     // Iron
            default => 131,         // Plastic
        };
    }

    private function getRankTier(int $rank): array
    {
        // Find the tier that matches this rank position
        // Priority: find exact match, then fallback to lowest tier (highest sort_order)
        $tier = RankingTier::where('min_rank', '<=', $rank)
            ->where(function ($query) use ($rank) {
                $query->whereNull('max_rank')
                    ->orWhere('max_rank', '>=', $rank);
            })
            ->orderBy('sort_order', 'asc') // Get highest tier that matches (lowest sort_order)
            ->first();

        if (!$tier) {
            // Fallback to the tier with the highest sort_order (lowest tier/worst rank)
            $tier = RankingTier::orderBy('sort_order', 'desc')->first();
        }

        return [
            'id' => $tier->id,
            'name' => $tier->name,
            'icon' => $tier->icon,
            'color' => $tier->color,
            'minRank' => $tier->min_rank,
            'maxRank' => $tier->max_rank,
        ];
    }
}
