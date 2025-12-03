<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class QuestsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get or create user streak
        $streak = $user->streak()->firstOrCreate([], [
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_login_date' => null,
            'last_login_at' => null,
        ]);
        
        // Fetch all quests from database
        $allQuests = Quest::all()->toArray();
        
        // All quests displayed in active tab
        $activeQuests = array_map(function($quest) {
            return array_merge($quest, [
                'objective' => $quest['title'],
                'progress' => 0,
                'status' => 'active',
                'reward' => $quest['xp'],
            ]);
        }, $allQuests);
        
        $completedQuests = array_map(function($quest) {
            return array_merge($quest, [
                'objective' => $quest['title'],
                'progress' => 100,
                'status' => 'completed',
                'reward' => $quest['xp'],
            ]);
        }, array_slice($allQuests, 0, 0));

        return Inertia::render('Quests', [
            'activeQuests' => $activeQuests,
            'completedQuests' => $completedQuests,
            'stats' => [
                'totalActive' => count($activeQuests),
                'totalCompleted' => count($completedQuests),
                'totalRewards' => array_sum(array_map(fn($q) => $q['xp'], $allQuests)),
                'streakDays' => 6,
            ],
            'streak' => [
                'currentStreak' => $streak->current_streak,
                'longestStreak' => $streak->longest_streak,
                'lastLoginDate' => $streak->last_login_date,
            ],
        ]);
    }
}
