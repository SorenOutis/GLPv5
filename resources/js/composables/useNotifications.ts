import { ref, computed, onUnmounted } from 'vue';
import axios from 'axios';

export interface DynamicNotification {
    id: number;
    type: 'course' | 'leaderboard' | 'achievement' | 'reward' | 'community' | 'assignment';
    title: string;
    message: string;
    timestamp: string;
    read: boolean;
    icon: string;
    data?: Record<string, any>;
}

const notifications = ref<DynamicNotification[]>([]);
const isLoading = ref(false);
const refreshInterval = ref<NodeJS.Timeout | null>(null);

export function useNotifications() {
    const unreadCount = computed(() => 
        notifications.value.filter(n => !n.read).length
    );

    const fetchNotifications = async () => {
        isLoading.value = true;
        try {
            const response = await axios.get('/api/notifications');
            notifications.value = response.data;
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
        try {
            await axios.post(`/api/notifications/${notificationId}/read`);
            const notification = notifications.value.find(n => n.id === notificationId);
            if (notification) {
                notification.read = true;
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    };

    const deleteNotification = async (notificationId: number) => {
        try {
            await axios.delete(`/api/notifications/${notificationId}`);
            notifications.value = notifications.value.filter(n => n.id !== notificationId);
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await axios.post('/api/notifications/mark-all-read');
            notifications.value.forEach(n => n.read = true);
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
