<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GamesManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin'); // Requires admin middleware
    }

    public function index(Request $request)
    {
        $games = Game::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->difficulty, function ($query, $difficulty) {
                $query->where('difficulty', $difficulty);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Games/Index', [
            'games' => $games,
            'filters' => $request->only(['search', 'difficulty']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Games/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'thumbnail' => 'required|url',
            'badge' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        Game::create($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game created successfully');
    }

    public function edit(Game $game)
    {
        return Inertia::render('Admin/Games/Edit', [
            'game' => $game,
        ]);
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'thumbnail' => 'required|url',
            'badge' => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $game->update($validated);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game updated successfully');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', 'Game deleted successfully');
    }

    public function toggle(Game $game)
    {
        $game->update(['is_active' => !$game->is_active]);

        return redirect()->back()
            ->with('success', 'Game status updated');
    }
}
