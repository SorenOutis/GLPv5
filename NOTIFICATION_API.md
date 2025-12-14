# Notification API Specification

The frontend expects the following API endpoints to provide dynamic notifications:

## Endpoints Required

### 1. GET `/api/notifications`
**Description:** Fetch all notifications for the current user

**Response:**
```json
[
  {
    "id": 1,
    "type": "course",
    "title": "New Course Available",
    "message": "Python Advanced Concepts is now available",
    "timestamp": "2 hours ago",
    "read": false,
    "icon": "📚"
  },
  {
    "id": 2,
    "type": "leaderboard",
    "title": "Rank Changed",
    "message": "You moved up 2 positions in the leaderboard",
    "timestamp": "1 hour ago",
    "read": false,
    "icon": "📈"
  },
  {
    "id": 3,
    "type": "achievement",
    "title": "Achievement Unlocked",
    "message": "You unlocked 'Quick Learner' badge",
    "timestamp": "3 hours ago",
    "read": true,
    "icon": "⭐"
  },
  {
    "id": 4,
    "type": "reward",
    "title": "Reward Earned",
    "message": "You earned 500 bonus XP",
    "timestamp": "1 day ago",
    "read": true,
    "icon": "🎁"
  },
  {
    "id": 5,
    "type": "community",
    "title": "Community Activity",
    "message": "Someone replied to your post",
    "timestamp": "2 days ago",
    "read": true,
    "icon": "💬"
  },
  {
    "id": 6,
    "type": "assignment",
    "title": "New Assignment Posted",
    "message": "Assignment: Data Structures is now available",
    "timestamp": "3 days ago",
    "read": true,
    "icon": "📝"
  }
]
```

### 2. POST `/api/notifications/{id}/read`
**Description:** Mark a specific notification as read

**Response:**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

### 3. DELETE `/api/notifications/{id}`
**Description:** Delete a specific notification

**Response:**
```json
{
  "success": true,
  "message": "Notification deleted"
}
```

### 4. POST `/api/notifications/mark-all-read`
**Description:** Mark all notifications as read for the current user

**Response:**
```json
{
  "success": true,
  "message": "All notifications marked as read"
}
```

## Notification Types

- **course**: New courses available, course updates
- **leaderboard**: Rank changes (up/down), position changes
- **achievement**: Achievement unlocked, badge earned
- **reward**: Reward earned, bonus XP gained
- **community**: Posts, comments, reactions in the community section
- **assignment**: New assignments posted, assignment updates

## Icon Recommendations

- Course: 📚
- Leaderboard: 📈 (rank up) or 📉 (rank down)
- Achievement: ⭐, 🏆
- Reward: 🎁, 💎
- Community: 💬, 👥
- Assignment: 📝, 📋

## Example Implementation (Laravel)

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
```

```php
// app/Http/Controllers/NotificationController.php
class NotificationController extends Controller
{
    public function index()
    {
        return auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'timestamp' => $notification->created_at->diffForHumans(),
                'read' => $notification->read_at !== null,
                'icon' => $this->getIcon($notification->type),
            ]);
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    public function destroy($id)
    {
        auth()->user()->notifications()->findOrFail($id)->delete();
        
        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    private function getIcon(string $type): string
    {
        return match($type) {
            'course' => '📚',
            'leaderboard' => '📈',
            'achievement' => '⭐',
            'reward' => '🎁',
            'community' => '💬',
            'assignment' => '📝',
            default => '🔔',
        };
    }
}
```
