<?php

namespace App\Http\Controllers;

use App\Models\CodingChallenge;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CodingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get user profile for XP and level data
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Plastic',
        ]);

        // Fetch active coding challenges from database
        $challenges = CodingChallenge::where('is_active', true)
            ->get()
            ->map(fn($challenge) => [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'difficulty' => $challenge->difficulty,
                'description' => $challenge->description,
                'xpReward' => $challenge->xp_reward,
                'completed' => false,
                'language' => $challenge->language,
                'category' => $challenge->category,
            ])
            ->toArray();

        // Prepare statistics
        $stats = [
            'totalChallenges' => count($challenges),
            'completedChallenges' => 0,
            'xpEarned' => 0,
            'currentStreak' => $profile->streak_days,
            'totalXP' => $profile->total_xp,
            'level' => $profile->level,
        ];

        // Prepare featured resources
        $resources = [
            [
                'id' => 1,
                'title' => 'JavaScript Basics',
                'description' => 'Learn the fundamentals of JavaScript programming.',
                'type' => 'Tutorial',
                'difficulty' => 'Beginner',
                'duration' => '2 hours',
            ],
            [
                'id' => 2,
                'title' => 'Advanced Algorithms',
                'description' => 'Master complex algorithmic problem-solving techniques.',
                'type' => 'Course',
                'difficulty' => 'Advanced',
                'duration' => '8 hours',
            ],
            [
                'id' => 3,
                'title' => 'Data Structures Deep Dive',
                'description' => 'Understand arrays, linked lists, trees, and graphs.',
                'type' => 'Tutorial',
                'difficulty' => 'Intermediate',
                'duration' => '4 hours',
            ],
        ];

        return Inertia::render('Coding', [
            'challenges' => $challenges,
            'stats' => $stats,
            'resources' => $resources,
            'userLevel' => $profile->level,
            'userName' => $user->name,
        ]);
    }

    public function show(CodingChallenge $challenge)
    {
        $formattedChallenge = [
            'id' => $challenge->id,
            'title' => $challenge->title,
            'difficulty' => $challenge->difficulty,
            'description' => $challenge->description,
            'problem_statement' => $challenge->problem_statement,
            'requirements' => $challenge->requirements ?? [],
            'example_input' => $challenge->example_input,
            'example_output' => $challenge->example_output,
            'tips' => $challenge->tips ?? [],
            'xpReward' => $challenge->xp_reward,
            'language' => $challenge->language,
            'category' => $challenge->category,
        ];

        return Inertia::render('ChallengeSolver', [
            'challenge' => $formattedChallenge,
        ]);
    }
}
