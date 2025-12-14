<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificationStreamController
{
    public function stream(Request $request)
    {
        $user = auth()->user();
        $lastId = $request->query('last_id', 0);

        return new StreamedResponse(function () use ($user, $lastId) {
            // Set headers for Server-Sent Events
            header('Cache-Control: no-cache');
            header('Content-Type: text/event-stream');
            header('X-Accel-Buffering: no');
            
            // Send initial message
            echo "data: " . json_encode(['type' => 'connected', 'message' => 'Connected to notification stream']) . "\n\n";
            flush();

            // Initial load of unread notifications
            $notifications = $user->notifications()
                ->where('id', '>', $lastId)
                ->get()
                ->map(fn($n) => [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'timestamp' => $n->created_at->diffForHumans(),
                    'read' => $n->isRead(),
                    'icon' => $n->icon,
                    'data' => $n->data,
                ]);

            if ($notifications->count() > 0) {
                foreach ($notifications as $notification) {
                    echo "data: " . json_encode(['type' => 'notification', 'notification' => $notification]) . "\n\n";
                    flush();
                }
            }

            // Keep connection alive for 1 hour, polling for new notifications
            $startTime = time();
            $timeout = 3600; // 1 hour
            $pollInterval = 5; // 5 seconds

            while ((time() - $startTime) < $timeout) {
                sleep($pollInterval);

                // Check for new notifications
                $latestNotification = $user->notifications()->latest('id')->first();
                $latestId = $latestNotification ? $latestNotification->id : $lastId;

                if ($latestId > $lastId) {
                    $newNotifications = $user->notifications()
                        ->where('id', '>', $lastId)
                        ->get()
                        ->map(fn($n) => [
                            'id' => $n->id,
                            'type' => $n->type,
                            'title' => $n->title,
                            'message' => $n->message,
                            'timestamp' => $n->created_at->diffForHumans(),
                            'read' => $n->isRead(),
                            'icon' => $n->icon,
                            'data' => $n->data,
                        ]);

                    foreach ($newNotifications as $notification) {
                        echo "data: " . json_encode(['type' => 'notification', 'notification' => $notification]) . "\n\n";
                        flush();
                        $lastId = $notification['id'];
                    }
                }

                // Send heartbeat to keep connection alive
                echo ": heartbeat\n\n";
                flush();
            }
        }, Response::HTTP_OK, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
