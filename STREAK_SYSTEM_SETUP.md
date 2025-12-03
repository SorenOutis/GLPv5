# Streak System Setup Guide

## Overview
The Streak System tracks user login streaks per day (12am to 12midnight). Users earn consecutive day streaks by logging in at least once per calendar day.

## Features Implemented

### 1. Database Migration
- **File:** `database/migrations/2025_12_03_120000_create_streaks_table.php`
- **Table:** `streaks`
- **Columns:**
  - `id` - Primary key
  - `user_id` - Foreign key to users table (cascade delete)
  - `current_streak` - Number of consecutive days (default: 0)
  - `longest_streak` - Longest streak achieved (default: 0)
  - `last_login_date` - Last date user logged in (nullable)
  - `last_login_at` - Last login timestamp (nullable)
  - `created_at`, `updated_at` - Timestamps

**Indices:** `current_streak`, `longest_streak` for efficient querying

### 2. Streak Model
- **File:** `app/Models/Streak.php`
- **Key Methods:**
  - `updateStreakOnLogin(User $user)` - Static method to update or create streak on login
  - `getCurrentStreak()` - Returns current streak count
  - `getLongestStreak()` - Returns longest streak count
  - `hasLoggedInToday()` - Checks if user logged in today
  - `resetStreak()` - Manually reset streak (admin function)

**Logic:**
- If user has not logged in today:
  - If last login was yesterday: increment current streak
  - If last login was before yesterday: reset current streak to 1
  - Update longest streak if current exceeds it

### 3. User Model Update
- **File:** `app/Models/User.php`
- Added relationship: `streak()` returns hasOne relationship

### 4. Admin Panel (Filament)
- **Files:**
  - `app/Filament/Resources/StreakResource.php` - Main resource
  - `app/Filament/Resources/StreakResource/Pages/ListStreaks.php`
  - `app/Filament/Resources/StreakResource/Pages/CreateStreak.php`
  - `app/Filament/Resources/StreakResource/Pages/EditStreak.php`

**Features:**
- View all user streaks with filtering
- Filter by active streaks (>0 days) or high streaks (>=30 days)
- Color-coded badges:
  - 🔥 30+ days (success/orange)
  - 🌡️ 14-29 days (info/blue)
  - ⚠️ 7-13 days (warning/yellow)
  - ⚡ 0-6 days (gray)
- Reset individual or multiple streaks
- Edit streak information directly
- Navigation icon: 🔥 Fire icon
- Located in admin menu with sort order 20

### 5. Middleware
- **File:** `app/Http/Middleware/UpdateUserStreak.php`
- Automatically updates streak on every authenticated user request
- Checks if user logged in today and updates accordingly
- Registered in `bootstrap/app.php` as web middleware

### 6. Observer
- **File:** `app/Observers/UserObserver.php`
- Automatically creates new streak record when a new user is registered
- Handles cascade deletion when user is deleted

### 7. Frontend Components
- **File:** `resources/js/components/StreakCard.vue`
- **Features:**
  - Displays current streak with emoji 🔥
  - Shows longest streak
  - Color-coded based on streak length
  - Milestone tracker (7, 14, 30 days)
  - Motivational messages
  - Responsive design with dark mode support

### 8. Dashboard Integration
- **File:** `app/Http/Controllers/DashboardController.php`
- Passes streak data to Dashboard page
- Data structure:
  ```php
  'streak' => [
      'currentStreak' => int,
      'longestStreak' => int,
      'lastLoginDate' => date|null,
  ]
  ```

## Installation Steps

### Step 1: Run Migration
```bash
php artisan migrate
```

This creates the `streaks` table in your database.

### Step 2: Create Streaks for Existing Users
For existing users without streak records, run:
```bash
php artisan tinker
# Then execute:
App\Models\User::all()->each(fn($user) => $user->streak()->firstOrCreate([], [
    'current_streak' => 0,
    'longest_streak' => 0,
    'last_login_date' => null,
    'last_login_at' => null,
]));
exit;
```

### Step 3: Update Dashboard
The Dashboard component has been updated to include:
- `streak` prop with current and longest streak data
- Props interface updated with `streak` field

### Step 4: Use StreakCard Component
In any Vue component, import and use:
```vue
<script setup lang="ts">
import StreakCard from '@/components/StreakCard.vue';

const props = defineProps<{
    streak?: {
        currentStreak: number;
        longestStreak: number;
        lastLoginDate: string | null;
    };
}>();
</script>

<template>
    <StreakCard :streak="streak" />
</template>
```

## Pages to Display Streaks

The streak system is ready to be integrated into:

1. **Dashboard** - Already integrated (via DashboardController)
2. **Quests** - Add StreakCard to `resources/js/pages/Quests.vue`
3. **Leaderboard** - Add streak column to `resources/js/pages/Leaderboard.vue`
4. **Progress** - Add StreakCard to `resources/js/pages/Progress.vue`
5. **Profile** - Add StreakCard to user profile `resources/js/pages/Profile.vue`

### Example Implementation for Other Pages

For **Quests** page:
```php
// In QuestsController
$user = auth()->user();
$streak = $user->streak;

return Inertia::render('Quests', [
    'streak' => [
        'currentStreak' => $streak->current_streak,
        'longestStreak' => $streak->longest_streak,
        'lastLoginDate' => $streak->last_login_date,
    ],
    // ... other data
]);
```

For **Leaderboard** page with streak column:
```php
// In LeaderboardController
$leaderboard = User::with('streak')
    ->orderBy('xp_earned', 'desc')
    ->get()
    ->map(fn($user) => [
        'name' => $user->name,
        'xp' => $user->profile->total_xp,
        'streak' => $user->streak->current_streak,
        'level' => $user->profile->level,
    ]);
```

## Streak Logic Explanation

### Daily Streak Tracking (12am - 12midnight)
- **Day boundary:** Calendar day (00:00 to 23:59 in app timezone)
- **Check:** `last_login_date` compared to `Carbon::now()->toDateString()`
- **Increment:** Only if last login was exactly 1 day ago
- **Reset:** If last login was 2+ days ago

### Streak Milestones
- 7 days: "Almost a week!"
- 14 days: "Great job!"
- 30 days: "Excellent work!"
- 30+ days: "LEGENDARY!"

### Color Coding
- `0 days`: Gray (no streak)
- `1-6 days`: Gray → Yellow
- `7-13 days`: Yellow
- `14-29 days`: Amber
- `30+ days`: Orange/Red (🔥)

## Admin Features

### Access Admin Panel
1. Navigate to `/admin`
2. Click on "Streaks" in the sidebar (🔥 icon)

### Actions Available
- **View Details:** Quick view of streak information
- **Reset Streak:** Reset user's current streak to 0 (requires confirmation)
- **Edit:** Manually edit streak values
- **Delete:** Remove streak record (cascades to user)
- **Bulk Reset:** Reset multiple streaks at once

### Filters
- **Active Streak:** Shows only users with current_streak > 0
- **High Streak:** Shows only users with current_streak >= 30

## Testing

### Test Streak Increment
```php
// In Tinker
$user = User::first();
$user->streak->update(['last_login_date' => now()->subDay()->toDateString()]);
// Visit dashboard - streak should increment by 1

// Test streak reset
$user->streak->update(['last_login_date' => now()->subDays(2)->toDateString()]);
// Visit dashboard - streak should reset to 1
```

### Test Admin Panel
1. Login as admin
2. Navigate to admin dashboard
3. Click "Streaks" menu item
4. Verify streaks display correctly
5. Test reset functionality

## Database Queries

### Get Top 10 Users by Current Streak
```php
$topStreaks = Streak::with('user')
    ->orderBy('current_streak', 'desc')
    ->take(10)
    ->get();
```

### Get All Users with Active Streaks
```php
$activeStreaks = Streak::where('current_streak', '>', 0)
    ->with('user')
    ->get();
```

### Get Legend Streaks (30+ days)
```php
$legendStreaks = Streak::where('current_streak', '>=', 30)
    ->with('user')
    ->get();
```

### Reset User's Streak Programmatically
```php
$user = User::find($id);
$user->streak()->update([
    'current_streak' => 0,
    'last_login_date' => null,
    'last_login_at' => null,
]);
```

## Troubleshooting

### Migration Fails
- Ensure all foreign key references exist
- Check database connection
- Run `php artisan migrate:refresh` if needed

### Streaks Not Updating
- Check middleware is registered in `bootstrap/app.php`
- Verify UpdateUserStreak middleware has no errors
- Check database for streak record creation

### Admin Panel Not Showing
- Verify StreakResource.php is in correct location
- Clear config cache: `php artisan config:cache`
- Restart Laravel application

### Streak Not Incrementing
- Verify `last_login_date` is updating correctly
- Check timezone configuration in `.env`
- Ensure middleware is processing before routes

## Future Enhancements

1. **Streak Achievements:** Unlock badges at 7, 14, 30+ day milestones
2. **Streak Notifications:** Remind users before midnight to maintain streak
3. **Streak Leaderboard:** Dedicated page for top streakers
4. **Freeze Days:** Allow users to skip one day without losing streak (premium)
5. **Streak Statistics:** Charts showing streak patterns over time
6. **Daily Login Bonus:** Award XP for daily logins based on streak
7. **Notification System:** Push notifications for streak milestones

## Files Created/Modified

### Created Files
- `database/migrations/2025_12_03_120000_create_streaks_table.php`
- `app/Models/Streak.php`
- `app/Http/Middleware/UpdateUserStreak.php`
- `app/Observers/UserObserver.php`
- `app/Filament/Resources/StreakResource.php`
- `app/Filament/Resources/StreakResource/Pages/ListStreaks.php`
- `app/Filament/Resources/StreakResource/Pages/CreateStreak.php`
- `app/Filament/Resources/StreakResource/Pages/EditStreak.php`
- `resources/js/components/StreakCard.vue`

### Modified Files
- `app/Models/User.php` - Added streak relationship
- `app/Http/Controllers/DashboardController.php` - Updated to include streak data
- `bootstrap/app.php` - Registered UpdateUserStreak middleware

## Support

For issues or questions about the streak system:
1. Check the Troubleshooting section
2. Review the database schema
3. Check middleware execution order
4. Verify timezone settings
