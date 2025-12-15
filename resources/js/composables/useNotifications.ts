import { ref, computed, onUnmounted } from 'vue';
import axios from 'axios';

export interface DynamicNotification {
    id: number;
    type: 'course' | 'leaderboard' | 'achievement' | 'reward' | 'community' | 'assignment' | 'announcement' | 'community_post';
    title: string;
    message: string;
    timestamp: string;
    read: boolean;
    icon: string;
    data?: Record<string, any>;
}

// Initialize from localStorage or as empty
const getInitialNotifications = (): DynamicNotification[] => {
    try {
        const stored = localStorage.getItem('app_notifications');
        return stored ? JSON.parse(stored) : [];
    } catch {
        return [];
    }
};

const notifications = ref<DynamicNotification[]>(getInitialNotifications());
const isLoading = ref(false);
const refreshInterval = ref<NodeJS.Timeout | null>(null);

export function useNotifications() {
    const unreadCount = computed(() => 
        notifications.value.filter(n => !n.read).length
    );

    const saveNotificationsToStorage = (notifs: DynamicNotification[]) => {
        try {
            localStorage.setItem('app_notifications', JSON.stringify(notifs));
        } catch (error) {
            console.error('Failed to save notifications to localStorage:', error);
        }
    };

    const fetchNotifications = async () => {
        isLoading.value = true;
        try {
            const [notificationsResponse, announcementsResponse] = await Promise.all([
                axios.get('/api/notifications'),
                axios.get('/api/announcements-and-posts/latest'),
            ]);
            
            // Combine notifications with announcements and community posts
            const combined = [
                ...notificationsResponse.data,
                ...announcementsResponse.data,
            ];
            
            // Sort by most recent
            combined.sort((a, b) => {
                const dateA = new Date(a.timestamp).getTime();
                const dateB = new Date(b.timestamp).getTime();
                return dateB - dateA;
            });
            
            notifications.value = combined;
            saveNotificationsToStorage(combined);
            isLoading.value = false;
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            isLoading.value = false;
        }
    };

    const setupRefreshInterval = () => {
        // Clear existing interval
        if (refreshInterval.value) {
            clearInterval(refreshInterval.value);
        }

        // Fetch initial notifications
        fetchNotifications();

        // Set up polling for real-time updates (3 seconds for instant feedback)
        refreshInterval.value = setInterval(() => {
            fetchNotifications();
        }, 3000);

        // Handle visibility changes - pause refresh when tab is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (refreshInterval.value) {
                    clearInterval(refreshInterval.value);
                    refreshInterval.value = null;
                }
            } else {
                // Resume refresh when tab becomes visible
                fetchNotifications();
                refreshInterval.value = setInterval(() => {
                    fetchNotifications();
                }, 3000);
            }
        });
    };

    const setupEventStream = setupRefreshInterval;

    const markAsRead = async (notificationId: number) => {
        // Update frontend state immediately
        const notification = notifications.value.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
        }

        // Persist to storage
        saveNotificationsToStorage(notifications.value);

        // Handle different notification types
        if (notificationId >= 10000) {
            // Community post
            try {
                await axios.post('/api/announcements/mark-read', {
                    announcement_id: notificationId - 10000,
                    type: 'community_post',
                });
            } catch (error) {
                console.error('Failed to mark community post as read:', error);
            }
        } else if (notification?.type === 'announcement') {
            // Announcement
            try {
                await axios.post('/api/announcements/mark-read', {
                    announcement_id: notificationId,
                    type: 'announcement',
                });
            } catch (error) {
                console.error('Failed to mark announcement as read:', error);
            }
        } else {
            // Regular notification
            try {
                await axios.post(`/api/notifications/${notificationId}/read`);
            } catch (error) {
                console.error('Failed to mark notification as read:', error);
            }
        }
    };

    const deleteNotification = async (notificationId: number) => {
        try {
            await axios.delete(`/api/notifications/${notificationId}`);
            notifications.value = notifications.value.filter(n => n.id !== notificationId);
            saveNotificationsToStorage(notifications.value);
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    };

    const markAllAsRead = async () => {
        // Mark all notifications in the frontend state
        notifications.value.forEach(n => n.read = true);
        
        // Persist to storage
        saveNotificationsToStorage(notifications.value);
        
        // Prepare items to mark as read
        const announcementItems = notifications.value.filter(n => n.type === 'announcement' || n.type === 'community_post');
        
        try {
            // Mark announcements and community posts as read
            if (announcementItems.length > 0) {
                await axios.post('/api/announcements/mark-all-read', {
                    items: announcementItems.map(item => ({
                        id: item.type === 'community_post' ? item.id - 10000 : item.id,
                        type: item.type,
                    })),
                });
            }
            
            // Mark all regular notifications as read in the database
            await axios.post('/api/notifications/mark-all-read');
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    };

    const getNotificationsByType = (type: DynamicNotification['type']) => {
        return notifications.value.filter(n => n.type === type);
    };

    // Cleanup on unmount
    onUnmounted(() => {
        if (refreshInterval.value) {
            clearInterval(refreshInterval.value);
            refreshInterval.value = null;
        }
    });

    return {
        notifications,
        unreadCount,
        isLoading,
        fetchNotifications,
        setupEventStream,
        markAsRead,
        deleteNotification,
        markAllAsRead,
        getNotificationsByType,
    };
}
