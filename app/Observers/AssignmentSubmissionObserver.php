<?php

namespace App\Observers;

use App\Models\AssignmentSubmission;

class AssignmentSubmissionObserver
{
    /**
     * Handle the AssignmentSubmission "created" event.
     */
    public function created(AssignmentSubmission $submission): void
    {
        $this->syncUserXP($submission);
    }

    /**
     * Handle the AssignmentSubmission "updated" event.
     */
    public function updated(AssignmentSubmission $submission): void
    {
        $this->syncUserXP($submission);
    }

    /**
     * Handle the AssignmentSubmission "deleted" event.
     */
    public function deleted(AssignmentSubmission $submission): void
    {
        $this->syncUserXP($submission);
    }

    /**
     * Sync user's total XP from all assignment submissions.
     */
    private function syncUserXP(AssignmentSubmission $submission): void
    {
        $user = $submission->user;
        
        // Get or create user profile
        $profile = $user->profile()->firstOrCreate([], [
            'total_xp' => 0,
            'level' => 1,
            'current_level_xp' => 0,
            'xp_for_next_level' => 1000,
            'streak_days' => 0,
            'rank_title' => 'Plastic',
        ]);

        // Calculate total XP from all assignment submissions with XP
        $assignmentXP = $user->assignmentSubmissions()
            ->whereNotNull('xp')
            ->sum('xp');

        // Update profile with total XP from assignments and recalculate level
        $profile->update([
            'total_xp' => $assignmentXP,
            'level' => max(1, intval($assignmentXP / 100) + 1),
            'current_level_xp' => $assignmentXP % 100,
        ]);
    }
}
