# Streak System - Quick Start Guide

## What's New

A complete user streak tracking system has been implemented. Users earn consecutive-day streaks by logging in each calendar day (12am-12midnight).

## Quick Setup (5 minutes)

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Create Streaks for Existing Users (Optional)
```bash
php artisan tinker
```
Then paste:
```php
use App\Models\User;
use App\Models\Streak;
User::all()->each(fn($user) => $user->streak()->firstOrCreate([], [
    'current_streak' => 0,
    'longest_streak' => 0,
    'last_login_date' => null,
    'last_login_at' => null,
]));
exit;
```

### 3. Done!
Streaks will automatically track from first login onwards.

## Key Files

| File | Purpose |
|------|---------|
| `database/migrations/2025_12_03_120000_create_streaks_table.php` | Database schema |
| `app/Models/Streak.php` | Streak model with logic |
| `app/Http/Middleware/UpdateUserStreak.php` | Auto-updates streaks on login |
| `app/Filament/Resources/StreakResource.php` | Admin panel for streaks |
| `resources/js/components/StreakCard.vue` | Reusable UI component |

## How It Works

1. **Automatic Tracking:** Middleware tracks logins automatically
2. **Daily Increment:** If user logs in consecutive days, streak increases
3. **Streak Reset:** If user misses a day, streak resets to 0
4. **Longest Streak:** Tracks the highest streak ever achieved

### Streak Logic
```
Day 1: streak = 1
Day 2: streak = 2
Day 3: SKIP
Day 4: streak = 1 (reset, because missed Day 3)
```

## Admin Panel

Access at: `/admin/streaks` (requires admin role)

### Features
- 🔥 View all user streaks
- 🏆 Filter by active/high streaks  
- ⚙️ Reset individual streaks
- 📊 Color-coded badges (30+ days = orange/fire)

## Display in Pages

### Dashboard (Already Done)
Streak card automatically displays with:
- Current streak
- Longest streak  
- Milestone tracker
- Motivational messages

### Add to Other Pages

**Quests Page:**
```php
// In QuestsController
return Inertia::render('Quests', [
    'streak' => [
        'currentStreak' => auth()->user()->streak->current_streak,
        'longestStreak' => auth()->user()->streak->longest_streak,
        'lastLoginDate' => auth()->user()->streak->last_login_date,
    ],
]);
```

**Leaderboard Page:**
```php
// Add to leaderboard query
$leaderboard->with('streak')->map(fn($user) => [
    'name' => $user->name,
    'streak' => $user->streak->current_streak,
    // ... other fields
]);
```

**Profile Page:**
```vue
<StreakCard :streak="userStreak" />
```

## Usage in Vue Components

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

## Database Queries

### Get Top 5 Streakers
```php
$topStreaks = Streak::with('user')
    ->orderBy('current_streak', 'desc')
    ->take(5)
    ->get();
```

### Get Legend Status (30+ days)
```php
$legends = Streak::where('current_streak', '>=', 30)->count();
```

### Reset User's Streak
```php
auth()->user()->streak->resetStreak();
```

## Features

✅ Automatic daily tracking  
✅ Current & longest streak stats  
✅ Admin panel management  
✅ Color-coded badges  
✅ Milestone notifications  
✅ Reusable Vue component  
✅ Cascade deletion with user  
✅ Dark mode support  

## Streak Milestones

| Streak | Status | Color |
|--------|--------|-------|
| 1-6 days | Getting started | Gray |
| 7 days | Almost a week | Yellow |
| 14 days | Great job | Amber |
| 30+ days | LEGENDARY | Orange/Fire 🔥 |

## Troubleshooting

**Streaks not updating?**
- Verify middleware in `bootstrap/app.php`
- Check database has `streaks` table
- Ensure `UpdateUserStreak` is running

**Admin panel not visible?**
- Clear cache: `php artisan config:cache`
- Verify you have admin role
- Check StreakResource.php exists

**Migration fails?**
- Ensure database connection works
- Run: `php artisan migrate --step`
- Check foreign key constraints

## Testing

```bash
# Test in tinker
php artisan tinker

# Get user's streak
$user = User::first();
$user->streak;

# Manually test streak update
$user->streak->update(['last_login_date' => now()->subDay()]);
Auth::login($user);
// Visit a page - streak should increment

# Reset user's streak
$user->streak->resetStreak();
```

## Next Steps

1. ✅ Run migration
2. ✅ Create existing user streaks (optional)
3. Add streak display to Quests page
4. Add streak display to Leaderboard page  
5. Add streak display to Progress page
6. Add streak display to Profile page
7. (Optional) Create streak achievements
8. (Optional) Add daily login bonus XP

## More Info

See `STREAK_SYSTEM_SETUP.md` for complete documentation.
