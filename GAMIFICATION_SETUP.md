# Gamification Dashboard Setup Guide

## Overview
Your dashboard is now fully integrated with a gamification system powered by Filament admin panel. All data is pulled dynamically from your database.

## Database Setup

Run the migrations to create the required tables:

```bash
php artisan migrate
```

This creates the following tables:
- **courses** - Course definitions (title, difficulty, XP rewards, etc.)
- **course_enrollments** - User enrollment in courses with progress tracking
- **lessons** - Individual lessons within courses
- **lesson_completions** - Track when users complete lessons
- **achievements** - Achievement/badge definitions
- **achievement_user** - Junction table for user achievements
- **user_profiles** - User gamification stats (XP, level, streaks, etc.)

## Filament Admin Resources

Two new resources are available in your Filament admin panel:

### 1. **Courses** (`/admin/courses`)
Manage all courses in your platform:
- Course title and description
- Difficulty level (beginner, intermediate, advanced, expert)
- Total number of lessons
- XP reward for completing the course
- Active status
- Thumbnail URL (optional)

### 2. **Achievements** (`/admin/achievements`)
Create and manage badges/achievements:
- Achievement title and description
- Icon (emoji)
- XP reward
- Unlock criteria description
- Active status

## Models & Relationships

### User Model
Extended with relationships:
```php
$user->profile() // User's gamification profile
$user->enrollments() // Courses user is enrolled in
$user->lessonCompletions() // Lessons completed by user
$user->achievements() // Achievements unlocked by user
```

### Course Model
- `enrollments()` - User enrollments
- `lessons()` - Lessons in course

### Other Models
- **CourseEnrollment** - Tracks user progress in courses
- **Lesson** - Individual course lessons
- **LessonCompletion** - User lesson completions
- **Achievement** - Badge definitions
- **UserProfile** - User gamification stats

## Dashboard Data Flow

### DashboardController (`app/Http/Controllers/DashboardController.php`)

The controller fetches and aggregates data:

1. **User Stats**
   - Total XP from UserProfile
   - Current level and progress
   - Streak count
   - Achievement count

2. **Active Courses**
   - Gets enrolled courses with progress
   - Shows completed lessons and XP earned
   - Displays next deadline

3. **Leaderboard**
   - Top 5 users ranked by total XP
   - Shows level and badge
   - Highlights current user

4. **Achievements**
   - All available achievements
   - Marks unlocked vs locked

5. **Recent Activity**
   - Latest lesson completions
   - Timestamps and XP rewards

### Vue Dashboard Component

The Dashboard.vue component receives props and displays:
- User stats cards (Level, Total XP, Streak, Achievements)
- Active courses with progress bars
- Recent activity feed
- Leaderboard with badges
- Achievement grid
- Next goal progress

## Usage Instructions

### Adding Courses
1. Go to **Admin Panel** → **Learning** → **Courses**
2. Click "Create"
3. Fill in:
   - Title (e.g., "Vue 3 Fundamentals")
   - Description
   - Total lessons count
   - Difficulty level
   - XP reward
   - Toggle active status
4. Save

### Adding Achievements
1. Go to **Admin Panel** → **Gamification** → **Achievements**
2. Click "Create"
3. Fill in:
   - Title (e.g., "First Steps")
   - Description
   - Icon (emoji, e.g., "👣")
   - Unlock criteria description
   - XP reward
   - Toggle active status
4. Save

### Enrolling Users in Courses
You'll need to create a course enrollment through code or add a Filament resource:

```php
$user->enrollments()->create([
    'course_id' => $courseId,
    'progress_percentage' => 0,
    'completed_lessons_count' => 0,
    'xp_earned' => 0,
]);
```

### Recording Lesson Completions
When a user completes a lesson:

```php
$user->lessonCompletions()->create([
    'lesson_id' => $lessonId,
    'completed_at' => now(),
    'score' => $score, // optional
]);

// Update user profile XP
$profile = $user->profile;
$profile->total_xp += $lesson->xp_reward;
$profile->save();
```

### Unlocking Achievements
When an achievement is unlocked:

```php
$user->achievements()->attach($achievementId, [
    'unlocked_at' => now(),
]);
```

## Customization

### Level System
Edit `DashboardController::getLevelBadge()` to customize badges per level:

```php
private function getLevelBadge(int $level): string
{
    return match (true) {
        $level >= 15 => '⭐',
        $level >= 12 => '🔥',
        $level >= 10 => '🚀',
        $level >= 5 => '💪',
        default => '⚡',
    };
}
```

### Dashboard Stats
All data in Dashboard.vue comes from controller props, making it easy to modify the display without touching the logic.

## Next Steps

1. **Run migrations**: `php artisan migrate`
2. **Create sample data** in the Filament admin panel
3. **Enroll users** in courses
4. **Build lesson completion logic** in your application
5. **Implement achievement checker** (e.g., via events/listeners)
6. **Create user engagement** by tracking streak days and updating profiles

## File Structure

```
app/
├── Models/
│   ├── Course.php
│   ├── CourseEnrollment.php
│   ├── Lesson.php
│   ├── LessonCompletion.php
│   ├── Achievement.php
│   └── UserProfile.php
├── Http/Controllers/
│   └── DashboardController.php
└── Filament/Resources/
    ├── CourseResource.php
    └── AchievementResource.php

resources/js/pages/
└── Dashboard.vue

database/migrations/
├── 2025_12_01_090000_create_courses_table.php
├── 2025_12_01_090100_create_course_enrollments_table.php
├── 2025_12_01_090200_create_lessons_table.php
├── 2025_12_01_090300_create_lesson_completions_table.php
├── 2025_12_01_090400_create_achievements_table.php
├── 2025_12_01_090500_create_achievement_user_table.php
└── 2025_12_01_090600_create_user_profiles_table.php
```

## Support

For more information on:
- **Filament**: https://filamentphp.com/
- **Laravel Models**: https://laravel.com/docs/eloquent
- **Vue 3**: https://vuejs.org/
- **Inertia.js**: https://inertiajs.com/
