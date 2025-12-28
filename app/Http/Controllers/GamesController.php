<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GamesController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get all active games with user progress
        $games = Game::where('is_active', true)
            ->with(['userProgresses' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->map(function ($game) use ($user) {
                $progress = $game->userProgresses->first();

                return [
                    'id' => $game->id,
                    'name' => $game->name,
                    'description' => $game->description,
                    'category' => $game->category,
                    'difficulty' => $game->difficulty,
                    'thumbnail' => $game->thumbnail,
                    'badge' => $game->badge,
                    'status' => $progress?->status ?? 'not_started',
                    'progress' => $progress?->progress ?? 0,
                    'timeSpent' => $progress?->time_spent_formatted ?? '0h 0m',
                    'rating' => $progress?->rating ?? 0,
                    'players' => $progress?->player_count ?? 0,
                ];
            });

        // Calculate stats
        $stats = [
            'totalGames' => $games->count(),
            'gamesPlaying' => $games->where('status', 'playing')->count(),
            'gamesCompleted' => $games->where('status', 'completed')->count(),
            'averageRating' => $games->avg('rating'),
            'totalTimePlayed' => $this->calculateTotalTime($games),
            'averageProgress' => (int) $games->avg('progress'),
        ];

        return Inertia::render('Games', [
            'games' => $games->values(),
            'stats' => $stats,
        ]);
    }

    private function calculateTotalTime($games): string
    {
        $totalSeconds = 0;
        foreach ($games as $game) {
            // Extract hours and minutes from timeSpent string like "4h 32m"
            if (preg_match('/(\d+)h\s*(\d+)m/', $game['timeSpent'], $matches)) {
                $totalSeconds += (int)$matches[1] * 3600 + (int)$matches[2] * 60;
            }
        }

        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        return sprintf('%dh %dm', $hours, $minutes);
    }
}
