# Notification System - Quick Start Guide

## 🚀 Quick Setup

### The system is already fully set up and working!

## 📝 How to Send Notifications

### 1. Course Available Notification
```php
use App\Services\NotificationService;

$course = Course::find(1);
$user = User::find(1);

NotificationService::notifyCourseAvailable($user, $course);
// Icon: 📚
// Type: 'course'
```

### 2. Leaderboard Rank Change
```php
NotificationService::notifyLeaderboardRankChange($user, 5, 3);
// Moved from rank 5 to rank 3 (up 2 positions)
// Icon: 📈 (if rank improved) or 📉 (if rank dropped)
// Type: 'leaderboard'
```

### 3. Achievement Unlocked
```php
$achievement = Achievement::find(1);
NotificationService::notifyAchievementUnlocked($user, $achievement);
// Icon: ⭐
// Type: 'achievement'
```

### 4. Reward Earned
```php
$reward = ['name' => '500 XP Bonus', 'amount' => 500];
NotificationService::notifyRewardEarned($user, $reward);
// Icon: 🎁
// Type: 'reward'
```

### 5. Community Activity
```php
NotificationService::notifyCommunityActivity($user, [
    'message' => 'Someone replied to your post',
    'icon' => '💬',
    'data' => ['post_id' => 123, 'commenter' => 'John Doe']
]);
// Type: 'community'
```

### 6. Assignment Posted
```php
$assignment = Assignment::find(1);
NotificationService::notifyAssignmentPosted($user, $assignment);
// Icon: 📝
// Type: 'assignment'
```

### 7. Level Up
```php
NotificationService::notifyLevelUp($user, 10, 250);
// Level 10 reached, +250 XP
// Icon: ⚡
// Type: 'achievement'
```

### 8. XP Gained
```php
NotificationService::notifyXPGained($user, 50, 'Completing assignment');
// +50 XP from "Completing assignment"
// Icon: ✨
// Type: 'achievement'
```

## 🔄 Where to Add Notifications

### Example: Course Controller
```php
public function enroll(Request $request, Course $course)
{
    $user = auth()->user();
    $user->enrollments()->attach($course);
    
    // Notify user of new course
    NotificationService::notifyCourseAvailable($user, $course);
    
    return redirect()->back();
}
```

### Example: Achievement Controller
```php
public function unlock(User $user, Achievement $achievement)
{
    if (!$user->achievements()->wherePivot('id', $achievement->id)->exists()) {
        $user->achievements()->attach($achievement);
        
        // Send real-time notification
        NotificationService::notifyAchievementUnlocked($user, $achievement);
    }
}
```

### Example: Leaderboard Update
```php
public function updateLeaderboard()
{
    $users = User::with('profile')->get();
    
    foreach ($users as $user) {
        $oldRank = $user->leaderboard_rank;
        $newRank = $this->calculateRank($user);
        
        if ($oldRank !== $newRank) {
            $user->update(['leaderboard_rank' => $newRank]);
            NotificationService::notifyLeaderboardRankChange($user, $oldRank, $newRank);
        }
    }
}
```

## 🎯 Real-Time Features

✅ **Instant Display** - Notifications appear without page refresh
✅ **Auto-Update Count** - Badge updates immediately
✅ **Persistent Connection** - SSE keeps connection open
✅ **Auto-Reconnect** - Handles disconnections gracefully
✅ **Database Stored** - All notifications saved per user

## 🔍 Frontend Usage

### In Vue Components
```vue
<script setup>
import { useNotifications } from '@/composables/useNotifications';

const { notifications, unreadCount, markAsRead, deleteNotification } = useNotifications();

const handleNotificationClick = (notificationId) => {
    markAsRead(notificationId);
};
</script>

<template>
    <div>
        <p>Unread: {{ unreadCount }}</p>
        <div v-for="notification in notifications" :key="notification.id">
            {{ notification.title }}: {{ notification.message }}
        </div>
    </div>
</template>
```

## 📊 Notification Types Reference

| Type | Icon | Use Case |
|------|------|----------|
| `course` | 📚 | New courses available |
| `leaderboard` | 📈/📉 | Rank changes |
| `achievement` | ⭐/⚡/✨ | Badges, level-ups, XP |
| `reward` | 🎁 | Bonuses, rewards earned |
| `community` | 💬 | Posts, replies, reactions |
| `assignment` | 📝 | New assignments posted |

## 🛠️ API Endpoints

```
GET    /api/notifications              - Get all notifications
GET    /api/notifications/stream        - Real-time SSE stream
POST   /api/notifications/{id}/read     - Mark as read
DELETE /api/notifications/{id}          - Delete notification
POST   /api/notifications/mark-all-read - Mark all as read
```

## 🗄️ Database Schema

```
notifications table:
- id (PK)
- user_id (FK) - Which user
- type - Notification type
- title - Short title
- message - Full message
- icon - Emoji icon
- data - JSON metadata
- read_at - When marked read (NULL = unread)
- created_at, updated_at
```

## ✅ Testing

Create a quick test:
```php
Route::get('/test-notification', function () {
    $user = auth()->user();
    \App\Services\NotificationService::notifyAchievementUnlocked(
        $user, 
        \App\Models\Achievement::first()
    );
    return 'Notification sent!';
});
```

Then visit `/test-notification` and check the notification bell!

## 📚 Complete Documentation

- `REALTIME_NOTIFICATIONS.md` - Full technical documentation
- `NOTIFICATION_API.md` - API specifications
- `NOTIFICATION_SETUP.md` - Setup details

---

**The real-time notification system is fully implemented and ready to use!**
