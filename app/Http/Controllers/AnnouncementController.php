<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class AnnouncementController extends Controller
{
    public function getLatest(): JsonResponse
    {
        $announcements = Announcement::where('is_active', true)
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'description' => $announcement->description ?? $announcement->content,
                'content' => $announcement->content,
                'timestamp' => $announcement->published_at?->format('M d, Y H:i') ?? $announcement->created_at->format('M d, Y H:i'),
                'type' => 'announcement',
            ]);

        return response()->json($announcements);
    }
}
