# Achievements System Setup

## Overview
Complete achievement system with seeding, dynamic categories, and locked/unlocked display states.

## Files Created/Modified

### 1. **Database Migration** - `database/migrations/2025_12_01_090400_create_achievements_table.php`
Updated with new fields:
- `name` - Achievement name
- `category` - Dynamic category for grouping (Learning, Streak, Milestones, Levels, XP, Social, Special)
- `difficulty` - Enum: Easy, Medium, Hard, Legendary
- `xp_reward` - XP points awarded

### 2. **Achievement Model** - `app/Models/Achievement.php`
Updated with:
- Proper fillable fields matching new schema
- Many-to-many relationship with User model via `achievement_user` pivot table
- Pivot includes `unlocked_at` timestamp

### 3. **AchievementSeeder** - `database/seeders/AchievementSeeder.php` (NEW)
Contains 20+ default achievements across 7 categories:
- **Learning** - Lesson completion milestones
- **Streak** - Consecutive learning day achievements
- **Milestones** - Achievement count milestones
- **Levels** - User level achievements
- **XP** - Experience point achievements
- **Social** - Community participation achievements
- **Special** - Unique/special achievements

### 4. **Achievements.vue Component** - `resources/js/pages/Achievements.vue`
Enhanced with:
- **All achievements displayed** - Locked achievements shown grayed out (opacity-50, grayscale filter)
- **Dynamic category tabs** - Automatically generated from achievement data
- **Dual filtering** - Filter by unlock status (All/Unlocked/Locked) AND category
- **Responsive grid** - 2 cols mobile, 3-4 cols desktop
- **Modal details** - Click any achievement to see full details
- **Smart sorting** - Unlocked achievements appear first, then alphabetical

## Features

### Visual States
- **Unlocked**: Full color, hover scale effect, green checkmark
- **Locked**: Grayed out (50% opacity), grayscale filter, shows difficulty badge
- **Hover**: Enhanced shadow and slight scaling on unlocked, increased opacity on locked

### Dynamic Elements
- Category tabs auto-generate from achievements database
- Tab counts update based on selected filter
- Empty states show contextual messaging

### Data Structure
```typescript
interface Achievement {
    id: number;
    name: string;
    description: string;
    icon: string;
    category: string;
    difficulty: 'Easy' | 'Medium' | 'Hard' | 'Legendary';
    unlocked: boolean;
    unlockedAt?: string;
    xp_reward: number;
}
```

## Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Run Seeder
```bash
php artisan db:seed --class=AchievementSeeder
```

Or add to `DatabaseSeeder.php`:
```php
$this->call(AchievementSeeder::class);
```

### 3. Controller Update
Update your achievements controller to pass `allAchievements` instead of separate `unlockedAchievements` and `lockedAchievements`:

```php
return Inertia::render('Achievements', [
    'allAchievements' => Achievement::with('users')
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
                ->contains(auth()->user()->id),
            'unlockedAt' => $achievement->users
                ->find(auth()->user()->id)?->pivot->unlocked_at?->format('M d, Y'),
        ]),
    'stats' => [
        'totalUnlocked' => auth()->user()->achievements()->count(),
        'totalAchievements' => Achievement::count(),
        'completionPercentage' => round(
            (auth()->user()->achievements()->count() / Achievement::count()) * 100
        ),
        'xpEarned' => auth()->user()->achievements()->sum('xp_reward'),
    ],
]);
```

## Categories

The seeder creates achievements across these categories (extensible):
- Learning (4 achievements)
- Streak (3 achievements)
- Milestones (3 achievements)
- Levels (3 achievements)
- XP (3 achievements)
- Social (3 achievements)
- Special (3 achievements)

## Customization

### Add New Achievements
Edit `database/seeders/AchievementSeeder.php` and add to the `$achievements` array:

```php
[
    'name' => 'Achievement Name',
    'description' => 'What it takes to unlock',
    'icon' => '🎯',
    'category' => 'Category Name',
    'difficulty' => 'Hard',
    'xp_reward' => 250,
],
```

### Adjust Styling
- Modify grid columns: `.grid-cols-2 md:grid-cols-3 lg:grid-cols-4`
- Difficulty colors: `getDifficultyColor()` function
- Hover effects: Card classes with `hover:scale-105 hover:shadow-lg`

## Notes
- Uses `firstOrCreate()` in seeder to avoid duplicates on re-runs
- Icons are emoji for universal support
- Difficulty enum enforced at database level
- All achievements accessible to view (locked ones shown with difficulty hint)
- Locked achievements show as 50% opacity with grayscale effect
