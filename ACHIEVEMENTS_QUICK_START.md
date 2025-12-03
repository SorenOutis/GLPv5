# Achievements System - Quick Start Guide

## What Was Done

### 1. ✅ Database Setup
- Migration: `2025_12_03_000000_update_achievements_table.php`
- Schema includes: name, description, icon, category, difficulty, xp_reward, is_active
- Status: **Applied** (22 achievements seeded)

### 2. ✅ Frontend User Page
- Vue component: `resources/js/pages/Achievements.vue`
- Features:
  - Shows ALL achievements (locked = grayed out 50% opacity)
  - Dynamic category tabs (auto-generated from database)
  - Filter by: All/Unlocked/Locked + Category
  - Click to view modal details
  - Sorted: unlocked first, then alphabetically

### 3. ✅ Admin Panel Page
- Resource: `app/Filament/Resources/AchievementResource.php`
- Features:
  - Full CRUD operations
  - Create new achievements on-the-fly
  - Search by name
  - Filter by category & difficulty
  - Color-coded badges
  - Active/inactive toggle
  - Access at: `/admin/achievements`

### 4. ✅ Seeder
- File: `database/seeders/AchievementSeeder.php`
- 22 default achievements across 7 categories
- Run: `php artisan db:seed --class=AchievementSeeder`

## Navigation

### User App
1. Dashboard/Main Menu → Achievements
2. View all achievements
3. Click achievement card for details

### Admin Panel
1. Login to admin
2. Left sidebar → Achievements (trophy icon)
3. Create/Edit/Delete achievements

## Key Features

### Locked Achievements
- Shown as gray (50% opacity)
- Grayscale filter applied
- Display difficulty badge (Easy/Medium/Hard/Legendary)
- Increase opacity on hover
- Can click to see details

### Unlocked Achievements
- Full color display
- Green checkmark with unlock date
- Scale up on hover
- Share button (in details modal)

### Admin Features
- Create achievement in seconds
- Change category/difficulty anytime
- Deactivate without deleting
- Search and filter
- Color-coded display

## Default Categories
1. **Learning** - Lesson completion (4 achievements)
2. **Streak** - Consecutive days (3 achievements)
3. **Milestones** - Achievement counts (3 achievements)
4. **Levels** - User level progression (3 achievements)
5. **XP** - Experience points (3 achievements)
6. **Social** - Community participation (3 achievements)
7. **Special** - Unique achievements (3 achievements)

## Controller Setup Required

Update your AchievementsController (or create one) with:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AchievementController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        
        return Inertia::render('Achievements', [
            'allAchievements' => Achievement::query()
                ->with('users')
                ->get()
                ->map(fn($achievement) => [
                    'id' => $achievement->id,
                    'name' => $achievement->name,
                    'description' => $achievement->description,
                    'icon' => $achievement->icon,
                    'category' => $achievement->category,
                    'difficulty' => $achievement->difficulty,
                    'xp_reward' => $achievement->xp_reward,
                    'unlocked' => $achievement->users
                        ->contains($currentUser->id),
                    'unlockedAt' => $achievement->users
                        ->find($currentUser->id)?->pivot->unlocked_at?->format('M d, Y'),
                ]),
            'stats' => [
                'totalUnlocked' => $currentUser->achievements()->count(),
                'totalAchievements' => Achievement::count(),
                'completionPercentage' => round(
                    ($currentUser->achievements()->count() / Achievement::count()) * 100
                ),
                'xpEarned' => $currentUser->achievements()
                    ->sum('xp_reward'),
            ],
        ]);
    }
}
```

## Routes Setup

Add to `routes/web.php`:

```php
Route::get('/achievements', [AchievementController::class, 'index'])
    ->middleware('auth')
    ->name('achievements.index');
```

## Testing Achievements

### In Admin Panel
1. Create test achievement
2. Set category & difficulty
3. Click save
4. Instantly appears in user app

### Unlock Achievement for User
1. Use database query or Eloquent
2. Attach achievement to user:

```php
$user->achievements()->attach($achievement->id, [
    'unlocked_at' => now()
]);
```

## Files Created/Modified

```
📁 Database
  └─ migrations/2025_12_03_000000_update_achievements_table.php (NEW)
  └─ migrations/2025_12_01_090400_create_achievements_table.php (UPDATED)
  └─ seeders/AchievementSeeder.php (NEW)

📁 App Models
  └─ Achievement.php (UPDATED)

📁 Filament Admin
  └─ Resources/AchievementResource.php (NEW)
  └─ Resources/AchievementResource/Pages/ListAchievements.php (NEW)
  └─ Resources/AchievementResource/Pages/CreateAchievement.php (NEW)
  └─ Resources/AchievementResource/Pages/EditAchievement.php (NEW)

📁 Frontend
  └─ resources/js/pages/Achievements.vue (UPDATED)

📁 Documentation
  └─ ACHIEVEMENTS_SETUP.md (NEW)
  └─ ADMIN_ACHIEVEMENTS_SETUP.md (NEW)
  └─ ACHIEVEMENTS_QUICK_START.md (THIS FILE)
```

## Next Steps

1. ✅ Database migrations applied
2. ✅ Seeder run (22 achievements)
3. ✅ Admin panel created
4. ✅ Frontend component updated
5. **TODO**: Create/update AchievementsController
6. **TODO**: Add route to web.php
7. **TODO**: Test unlocking achievements for users

## Troubleshooting

### Admin page not showing
- Check if Filament is properly installed
- Run: `php artisan cache:clear`
- Refresh admin panel

### Achievements not displaying
- Verify route and controller
- Check Inertia props match component
- Inspect browser console for errors

### Categories not showing as tabs
- Categories are auto-generated from database
- If no achievements exist, no tabs show
- Create achievements in admin to test

## Customization Examples

### Add New Category
1. Edit admin form: `AchievementResource.php` (line 45)
2. Add option: `'NewCategory' => 'NewCategory'`
3. Create achievement with new category

### Change Difficulty Colors
Edit `AchievementResource.php` table columns, change:
```php
'Easy' => 'green',      // Change to 'red', 'blue', etc.
'Medium' => 'blue',
'Hard' => 'orange',
'Legendary' => 'purple',
```

### Adjust Locked Achievement Styling
Edit `Achievements.vue` template, find `.opacity-50` class and modify Tailwind classes.

## Questions?

Refer to:
- `ACHIEVEMENTS_SETUP.md` - Detailed setup
- `ADMIN_ACHIEVEMENTS_SETUP.md` - Admin features
- Component: `resources/js/pages/Achievements.vue`
- Resource: `app/Filament/Resources/AchievementResource.php`
