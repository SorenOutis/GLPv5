<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display the current user's profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        $userStreak = $user->streak;

        return Inertia::render('User', [
            'isOwnProfile' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => null,
                'bio' => optional($user->profile)->bio,
                'cover_photo_path' => $user->cover_photo_path,
                'profile_photo_path' => $user->profile_photo_path,
                'joinedDate' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'totalXP' => optional($user->profile)->total_xp ?? 0,
                'level' => optional($user->profile)->level ?? 1,
                'coursesEnrolled' => $user->enrollments()->count(),
                'assignmentsCompleted' => $user->lessonCompletions()->count(),
                'achievementsUnlocked' => $user->achievements()->count(),
                'currentStreak' => $userStreak ? $userStreak->current_streak : 0,
                'rank' => optional($user->profile)->rank ?? 0,
                'rankTitle' => optional($user->profile)->rank_title ?? 'Novice',
            ],
            'progress' => $user->enrollments()
                ->with('course')
                ->get()
                ->map(function ($enrollment) {
                    $totalLessons = $enrollment->course->lessons()->count();
                    $completedLessons = $enrollment->user->lessonCompletions()
                        ->whereHas('lesson', function ($query) use ($enrollment) {
                            $query->where('course_id', $enrollment->course_id);
                        })
                        ->count();

                    return [
                        'courseId' => $enrollment->course_id,
                        'courseName' => $enrollment->course->name,
                        'progress' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
                        'completedLessons' => $completedLessons,
                        'totalLessons' => $totalLessons,
                    ];
                }),
            'achievements' => $user->achievements()
                ->orderByPivot('unlocked_at', 'desc')
                ->get()
                ->map(function ($achievement) {
                    $unlockedAt = $achievement->pivot?->unlocked_at;
                    $formattedDate = is_string($unlockedAt) ? $unlockedAt : ($unlockedAt ? $unlockedAt->format('M d, Y') : 'Unlocked');
                    
                    return [
                        'id' => $achievement->id,
                        'name' => $achievement->name,
                        'description' => $achievement->description,
                        'icon' => $achievement->icon ?? '⭐',
                        'unlockedAt' => $formattedDate,
                    ];
                }),
        ]);
    }

    /**
     * Display all users (admin view).
     */
    public function index()
    {
        $users = User::with('profile', 'achievements')->paginate(15);

        return Inertia::render('Users/List', [
            'users' => $users->through(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'totalXP' => optional($user->profile)->total_xp ?? 0,
                    'level' => optional($user->profile)->level ?? 1,
                    'achievements' => $user->achievements()->count(),
                    'joinedDate' => $user->created_at->format('M d, Y'),
                ];
            }),
            'total' => $users->total(),
        ]);
    }

    /**
     * Display a specific user's profile.
     */
    public function show(User $user)
    {
        $userStreak = $user->streak;

        return Inertia::render('User', [
            'isOwnProfile' => $user->id === Auth::id(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => null,
                'bio' => optional($user->profile)->bio,
                'cover_photo_path' => $user->cover_photo_path,
                'profile_photo_path' => $user->profile_photo_path,
                'joinedDate' => $user->created_at->format('M d, Y'),
            ],
            'stats' => [
                'totalXP' => optional($user->profile)->total_xp ?? 0,
                'level' => optional($user->profile)->level ?? 1,
                'coursesEnrolled' => $user->enrollments()->count(),
                'assignmentsCompleted' => $user->lessonCompletions()->count(),
                'achievementsUnlocked' => $user->achievements()->count(),
                'currentStreak' => $userStreak ? $userStreak->current_streak : 0,
                'rank' => optional($user->profile)->rank ?? 0,
                'rankTitle' => optional($user->profile)->rank_title ?? 'Novice',
            ],
            'progress' => $user->enrollments()
                ->with('course')
                ->get()
                ->map(function ($enrollment) {
                    $totalLessons = $enrollment->course->lessons()->count();
                    $completedLessons = $enrollment->user->lessonCompletions()
                        ->whereHas('lesson', function ($query) use ($enrollment) {
                            $query->where('course_id', $enrollment->course_id);
                        })
                        ->count();

                    return [
                        'courseId' => $enrollment->course_id,
                        'courseName' => $enrollment->course->name,
                        'progress' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0,
                        'completedLessons' => $completedLessons,
                        'totalLessons' => $totalLessons,
                    ];
                }),
            'achievements' => $user->achievements()
                ->orderByPivot('unlocked_at', 'desc')
                ->get()
                ->map(function ($achievement) {
                    $unlockedAt = $achievement->pivot?->unlocked_at;
                    $formattedDate = is_string($unlockedAt) ? $unlockedAt : ($unlockedAt ? $unlockedAt->format('M d, Y') : 'Unlocked');
                    
                    return [
                        'id' => $achievement->id,
                        'name' => $achievement->name,
                        'description' => $achievement->description,
                        'icon' => $achievement->icon ?? '⭐',
                        'unlockedAt' => $formattedDate,
                    ];
                }),
        ]);
    }
}
