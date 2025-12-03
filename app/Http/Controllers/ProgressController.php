<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ProgressMetric;
use App\Models\Streak;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile;

        // Get user stats
        $assignmentsCompleted = AssignmentSubmission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        
        $coursesEnrolled = CourseEnrollment::where('user_id', $user->id)->count();
        
        $assignments = Assignment::count();
        
        $xpGained = AssignmentSubmission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('xp_earned') ?? 0;

        // Calculate average completion rate
        $enrollments = CourseEnrollment::where('user_id', $user->id)->get();
        $averageCompletionRate = $enrollments->count() > 0 
            ? (int) $enrollments->average('progress_percentage') 
            : 0;

        // Get streak data
        $streak = Streak::where('user_id', $user->id)->first();

        // Get metrics and update XP Gained with actual user XP and Current Streak
        $metrics = ProgressMetric::getActive()->toArray();
        $metrics = array_map(function($metric) use ($profile, $streak) {
            if ($metric['name'] === 'XP Gained') {
                $metric['current'] = $profile?->total_xp ?? 0;
            }
            if ($metric['name'] === 'Current Streak') {
                $metric['current'] = $streak?->current_streak ?? 0;
            }
            return $metric;
        }, $metrics);

        return Inertia::render('Progress', [
            'levelProgress' => [
                'currentLevel' => $profile?->level ?? 1,
                'nextLevel' => ($profile?->level ?? 1) + 1,
                'currentXP' => $profile?->current_level_xp ?? 0,
                'xpForNextLevel' => $profile?->xp_for_next_level ?? 1000,
                'totalXPEarned' => $profile?->total_xp ?? 0,
            ],
            'metrics' => $metrics,
            'recentMilestones' => [
                [
                    'date' => 'Jan 2, 2024',
                    'title' => 'Reached Level ' . ($profile?->level ?? 1),
                    'description' => 'You achieved level ' . ($profile?->level ?? 1) . ' through consistent learning!',
                ],
                [
                    'date' => 'Jan 1, 2024',
                    'title' => 'New Year, New Goals',
                    'description' => 'Happy New Year! Your learning journey continues.',
                ],
                [
                    'date' => 'Dec 31, 2023',
                    'title' => 'Course Completed',
                    'description' => 'You completed a course with excellent progress.',
                ],
                [
                    'date' => 'Dec 28, 2023',
                    'title' => 'Assignment Completed',
                    'description' => 'You completed ' . $assignmentsCompleted . ' assignments so far!',
                ],
            ],
            'stats' => [
                'totalAssignmentsCompleted' => $assignmentsCompleted,
                'totalCoursesEnrolled' => $coursesEnrolled,
                'totalAssignmentsAvailable' => $assignments,
                'averageCompletionRate' => $averageCompletionRate,
            ],
        ]);
    }
}
