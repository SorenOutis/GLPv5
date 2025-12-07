<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = CommunityPost::where('is_approved', true)
            ->with('user', 'reactions', 'views')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(10);

        $stats = [
            'totalPosts' => CommunityPost::where('is_approved', true)->count(),
            'activeCommunity' => CommunityPost::where('is_approved', true)
                ->whereDate('created_at', now()->toDateString())
                ->count(),
            'topContributor' => CommunityPost::where('is_approved', true)
                ->selectRaw('user_id, COUNT(*) as post_count')
                ->groupBy('user_id')
                ->orderByDesc('post_count')
                ->first()?->user?->name ?? 'Community',
        ];

        return Inertia::render('Community', [
            'posts' => $posts,
            'stats' => $stats,
        ]);
    }

    public function show(CommunityPost $post)
    {
        $userId = Auth::id();
        
        // Track view (one per user)
        if (!$post->hasUserViewed($userId)) {
            \App\Models\PostView::create([
                'community_post_id' => $post->id,
                'user_id' => $userId,
            ]);
        }
        
        return Inertia::render('CommunityDetail', [
            'post' => $post->load('user', 'reactions'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $post = Auth::user()->communityPosts()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => substr($validated['content'], 0, 150) . '...',
            'status' => 'pending',
            'is_approved' => false,
        ]);

        return redirect()->route('community')->with('success', 'Post submitted for approval!');
    }

    public function update(Request $request, CommunityPost $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
        ]);

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => substr($validated['content'], 0, 150) . '...',
        ]);

        return redirect()->route('community')->with('success', 'Post updated!');
    }

    public function destroy(CommunityPost $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('community')->with('success', 'Post deleted!');
    }

    public function react(Request $request, CommunityPost $post)
    {
        $validated = $request->validate([
            'reaction_type' => 'required|in:like,love,haha,wow,sad,angry',
        ]);

        $userId = Auth::id();
        
        // Check if user already reacted
        $existingReaction = $post->userReaction($userId);
        
        if ($existingReaction) {
            // If same reaction, remove it; otherwise update it
            if ($existingReaction->reaction_type === $validated['reaction_type']) {
                $existingReaction->delete();
            } else {
                $existingReaction->update(['reaction_type' => $validated['reaction_type']]);
            }
        } else {
            // Create new reaction
            \App\Models\CommunityPostReaction::create([
                'community_post_id' => $post->id,
                'user_id' => $userId,
                'reaction_type' => $validated['reaction_type'],
            ]);
        }
        
        return back();
    }
}
