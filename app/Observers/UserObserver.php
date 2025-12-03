<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Streak;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Create a new streak record for new user
        Streak::create([
            'user_id' => $user->id,
            'current_streak' => 0,
            'longest_streak' => 0,
            'last_login_date' => null,
            'last_login_at' => null,
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Delete streak when user is deleted (cascade handled in migration)
    }
}
