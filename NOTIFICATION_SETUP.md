# Notification System - Setup Complete ✓

## Files Created

### Database
- `database/migrations/2025_12_14_172459_create_notifications_table.php` - Notifications table schema

### Models
- `app/Models/Notification.php` - Notification model with relationships and methods
- `app/Models/User.php` - Updated with `notifications()` relationship

### Services
- `app/Services/NotificationService.php` - Service class with static methods for creating notifications

### Controllers
- `app/Http/Controllers/Api/NotificationController.php` - API controller for notification endpoints

### Routes
- `routes/web.php` - Updated with notification API routes

## Database Schema

```
notifications table:
- id (primary key)
- user_id (foreign key)
- type (string: 'course', 'leaderboard', 'achievement', 'reward', 'community', 'assignment')
- title (string)
- message (text)
- icon (string, default: '🔔')
- data (json, nullable) - for storing additional data
- read_at (timestamp, nullable) - null = unread
- created_at, updated_at (timestamps)
```

## API Endpoints

All endpoints require authentication.

### GET `/api/notifications`
Fetch all notifications for the current user
- Returns array of notifications sorted by newest first

### POST `/api/notifications/{id}/read`
Mark a specific notification as read

### DELETE `/api/notifications/{id}`
Delete a specific notification

### POST `/api/notifications/mark-all-read`
Mark all unread notifications as read

## Usage Examples

### In Your Controllers/Services

```php
use App\Services\NotificationService;

// Notify when course is available
NotificationService::notifyCourseAvailable($user, $course);

// Notify when rank changes
NotificationService::notifyLeaderboardRankChange($user, 5, 3); // moved from rank 5 to 3 (up 2)

// Notify achievement unlock
NotificationService::notifyAchievementUnlocked($user, $achievement);

// Notify reward earned
NotificationService::notifyRewardEarned($user, $reward);

// Notify community activity
NotificationService::notifyCommunityActivity($user, [
    'message' => 'Someone replied to your post',
    'icon' => '💬',
    'data' => ['post_id' => 123]
]);

// Notify assignment posted
NotificationService::notifyAssignmentPosted($user, $assignment);

// Notify level up
NotificationService::notifyLevelUp($user, 10, 250); // level 10, +250 XP

// Notify XP gained
NotificationService::notifyXPGained($user, 50, 'Completing assignment');
```

## Frontend Integration

The frontend notification system is already set up in:
- `resources/js/composables/useNotifications.ts` - Composable for managing notifications
- `resources/js/components/AppSidebarHeader.vue` - Notification bell with dropdown

## Features

✓ Database persistence - notifications saved per user
✓ Read/Unread status tracking
✓ Mark individual notifications as read
✓ Mark all notifications as read
✓ Delete notifications
✓ Automatic timestamps (created_at, read_at)
✓ Flexible data storage (JSON)
✓ Icon customization
✓ Type categorization

## Next Steps

1. Use `NotificationService` methods whenever relevant events occur:
   - When a course is created/assigned to user
   - When user's leaderboard rank changes
   - When user unlocks an achievement
   - When user earns a reward
   - When community activity happens
   - When new assignments are posted

2. Example in a Course Controller:
```php
public function enroll(Course $course)
{
    // Enroll user...
    
    // Notify other users about new enrollment
    // Or notify user about new course
}
```

3. Example in a Leaderboard Update Process:
```php
public function updateLeaderboard()
{
    $users = User::all();
    
    foreach ($users as $user) {
        $oldRank = $user->current_rank;
        // Update rank logic...
        $newRank = $user->current_rank;
        
        if ($oldRank !== $newRank) {
            NotificationService::notifyLeaderboardRankChange($user, $oldRank, $newRank);
        }
    }
}
```

## Migration Status
✓ Migration completed and applied to database
✓ Notifications table created successfully
✓ Ready to start creating notifications
