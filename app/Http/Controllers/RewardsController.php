<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RewardsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all achievements
        $allAchievements = Achievement::where('is_active', true)->get();

        // Get user's unlocked achievements with pivot data
        $unlockedAchievements = $user->achievements()->wherePivot('unlocked_at', '!=', null)->get();

        // Build badges with locked/unlocked status and earned dates
         $badges = $allAchievements->map(function ($achievement) use ($unlockedAchievements) {
             $isUnlocked = $unlockedAchievements->contains('id', $achievement->id);
             $pivot = $isUnlocked ? $unlockedAchievements->find($achievement->id)->pivot : null;
             
             $earnedAt = null;
             if ($isUnlocked && $pivot && $pivot->unlocked_at) {
                 $earnedAt = is_string($pivot->unlocked_at) ? $pivot->unlocked_at : $pivot->unlocked_at->format('M d, Y');
             }
         
             return [
                 'id' => $achievement->id,
                 'name' => $achievement->name,
                 'description' => $achievement->description,
                 'icon' => $achievement->icon,
                 'rarity' => strtolower($achievement->difficulty),
                 'isUnlocked' => $isUnlocked,
                 'earnedAt' => $earnedAt,
                 'xpReward' => $achievement->xp_reward,
                 'category' => $achievement->category,
             ];
         })->sortByDesc('isUnlocked')->values();

        // Get recent rewards (unlocked achievements ordered by recent)
        $rewards = $unlockedAchievements
            ->sortByDesc(function ($achievement) {
                return $achievement->pivot->unlocked_at;
            })
            ->take(10)
            ->map(function ($achievement) {
                $unlockedAt = $achievement->pivot->unlocked_at;
                $earnedAt = is_string($unlockedAt) ? $unlockedAt : ($unlockedAt ? $unlockedAt->format('M d, Y') : 'Unlocked');
                
                return [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'type' => strtolower($achievement->category),
                    'earnedAt' => $earnedAt,
                    'xpValue' => $achievement->xp_reward,
                ];
            })
            ->values();

        // Calculate stats
        $totalBadges = $unlockedAchievements->count();
        $totalXPFromRewards = $unlockedAchievements->sum('xp_reward');
        $nextMilestoneCount = $allAchievements->count();
        $progressPercentage = $nextMilestoneCount > 0 ? ceil(($totalBadges / $nextMilestoneCount) * 100) : 0;

        // Save XP to user_profiles table
        $userProfile = $user->profile;
        if ($userProfile) {
            $userProfile->update([
                'total_xp' => $totalXPFromRewards,
            ]);
        }

        $stats = [
            'totalRewards' => $totalBadges,
            'totalBadges' => $totalBadges,
            'totalXPFromRewards' => $totalXPFromRewards,
            'nextMilestone' => "Complete {$nextMilestoneCount} Achievements ({$progressPercentage}%)",
        ];

        return Inertia::render('Rewards', [
            'rewards' => $rewards,
            'badges' => $badges,
            'stats' => $stats,
        ]);
    }
}
