# Real-Time Notification System

## Overview

The notification system now supports **real-time, dynamic updates** without requiring page refresh. Notifications appear instantly when they are created in the system.

## Architecture

### Backend Components

1. **NotificationCreated Event** (`app/Events/NotificationCreated.php`)
   - Fires automatically whenever a notification is created
   - Broadcasts to the user's private channel

2. **NotificationStreamController** (`app/Http/Controllers/Api/NotificationStreamController.php`)
   - Implements Server-Sent Events (SSE) for real-time streaming
   - Maintains persistent connection with client
   - Polls database for new notifications every 5 seconds
   - Connection timeout: 1 hour with automatic reconnect

3. **NotificationController** (`app/Http/Controllers/Api/NotificationController.php`)
   - Provides REST endpoints for notification management
   - Handles mark as read, delete, mark all as read

### Frontend Components

1. **useNotifications Composable** (`resources/js/composables/useNotifications.ts`)
   - `fetchNotifications()` - Initial load of all notifications
   - `setupEventStream()` - Establishes real-time connection
   - Listens to `notification.created` events
   - Auto-reconnect on connection loss

2. **AppSidebarHeader** (`resources/js/components/AppSidebarHeader.vue`)
   - Initializes notification stream on mount
   - Displays real-time notification count badge
   - Shows notifications as they arrive

## How It Works

```
User Action (e.g., unlock achievement)
    ↓
NotificationService::notifyAchievementUnlocked($user, $achievement)
    ↓
Notification::create() [saved to database]
    ↓
NotificationCreated event fires
    ↓
Event broadcasted to user's channel
    ↓
Frontend receives via SSE
    ↓
Notification appears instantly (no refresh needed)
```

## Real-Time Flow

1. **Initial Load** (on page load)
   - `fetchNotifications()` loads all existing notifications from database
   - Updates unread count badge

2. **Event Stream Setup** (on mount)
   - `setupEventStream()` establishes SSE connection
   - Connection stays open, listening for new notifications

3. **New Notification Created**
   - Backend creates `Notification` record
   - Event fires and broadcasts
   - Frontend receives via SSE `notification.created` event
   - New notification unshifted to beginning of list
   - Unread count updated automatically

4. **Auto-Reconnect**
   - If connection drops, frontend automatically reconnects after 5 seconds
   - No manual intervention needed

## API Endpoints

### GET `/api/notifications/stream` (SSE)
Server-Sent Events stream for real-time notifications.

**Query Parameters:**
- `last_id` (optional) - Only return notifications after this ID

**Response (Stream):**
```
event: notification.created
data: {"type":"notification","notification":{...}}

event: message
data: {"type":"connected","message":"Connected to notification stream"}
```

### GET `/api/notifications`
Fetch all notifications (initial load)

### POST `/api/notifications/{id}/read`
Mark notification as read

### DELETE `/api/notifications/{id}`
Delete notification

### POST `/api/notifications/mark-all-read`
Mark all notifications as read

## Creating Notifications

Use the `NotificationService` in your application code:

```php
use App\Services\NotificationService;

// Example 1: Achievement Unlocked
if ($userUnlockedAchievement) {
    NotificationService::notifyAchievementUnlocked($user, $achievement);
}

// Example 2: Rank Changed
$oldRank = $user->leaderboard_rank;
$user->update(['leaderboard_rank' => $newRank]);
NotificationService::notifyLeaderboardRankChange($user, $oldRank, $newRank);

// Example 3: New Course Available
NotificationService::notifyCourseAvailable($user, $course);

// Example 4: Custom Notification
Notification::create([
    'user_id' => $user->id,
    'type' => 'custom',
    'title' => 'Custom Title',
    'message' => 'Custom message',
    'icon' => '🎯',
    'data' => ['custom_field' => 'value'],
]);
```

## Event Listeners

If you need to run custom logic when notifications are created, create event listeners:

```php
// app/Listeners/SendNotificationEmail.php
namespace App\Listeners;

use App\Events\NotificationCreated;

class SendNotificationEmail
{
    public function handle(NotificationCreated $event): void
    {
        // Send email or push notification
    }
}

// Register in EventServiceProvider
protected $listen = [
    NotificationCreated::class => [
        SendNotificationEmail::class,
    ],
];
```

## Features

✅ **Real-Time Updates** - Notifications appear instantly
✅ **No Page Refresh Required** - Uses Server-Sent Events
✅ **Auto-Reconnect** - Handles connection drops gracefully
✅ **Persistent Storage** - Saved in database per user
✅ **Unread Tracking** - Automatic unread count
✅ **Event-Based** - Fires on notification creation
✅ **Type Categorization** - course, leaderboard, achievement, reward, community, assignment
✅ **Custom Data** - Store additional JSON data with each notification

## Performance Considerations

- **SSE Connection Timeout**: 1 hour (auto-reconnect)
- **Polling Interval**: 5 seconds (configurable in NotificationStreamController)
- **Initial Load**: Only on page load (cached for 30 seconds)
- **Database Indexes**: `user_id` and `read_at` indexed for fast queries

## Troubleshooting

### Notifications not appearing
1. Check browser console for errors
2. Verify `/api/notifications/stream` endpoint is accessible
3. Check `read_at` field is NULL for unread notifications

### Connection keeps dropping
1. Check server timeout settings in `NotificationStreamController`
2. Verify firewall/proxy allows persistent connections
3. Check browser console for SSE errors

### High memory/CPU usage
1. Increase polling interval in `NotificationStreamController`
2. Implement notification archival (delete old notifications)
3. Consider using Laravel WebSockets or Pusher for production

## Production Notes

For production deployment:

1. **WebSockets Option** (Recommended)
   - Replace SSE with Laravel WebSockets
   - Better scalability with multiple server instances
   - Lower latency

2. **Pusher Integration** (Alternative)
   - Managed WebSocket service
   - Easier scaling
   - Reduces server load

3. **Database Cleanup**
   - Implement command to archive old notifications
   - Use database view for read notifications

Example command:
```php
// app/Console/Commands/ArchiveOldNotifications.php
$notifications = Notification::whereNotNull('read_at')
    ->where('created_at', '<', now()->subDays(30))
    ->delete();
```

## Testing

Test real-time notifications:

```php
// In your test
use App\Models\User;
use App\Services\NotificationService;

$user = User::first();
NotificationService::notifyAchievementUnlocked($user, $achievement);

// Frontend should show notification instantly
```

## Migration Status

✅ Database table created
✅ Model and relationships set up
✅ Service class implemented
✅ Event system configured
✅ API endpoints created
✅ Real-time streaming working
✅ Frontend integration complete
