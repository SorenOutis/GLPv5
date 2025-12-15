<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useNotifications } from '@/composables/useNotifications';

const { props } = usePage();
const { notifications, fetchNotifications, markAsRead, deleteNotification } = useNotifications();
const isLoading = ref(false);

onMounted(async () => {
    isLoading.value = true;
    await fetchNotifications();
    isLoading.value = false;
});

const handleMarkAsRead = async (id: number) => {
    await markAsRead(id);
};

const handleDelete = async (id: number) => {
    await deleteNotification(id);
};

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <div class="flex flex-col gap-6 px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-foreground">Notifications</h1>
                <p class="text-sm text-muted-foreground mt-1">
                    You have {{ notifications.filter(n => !n.read).length }} unread notifications
                </p>
            </div>
            <button
                @click="goBack"
                class="text-muted-foreground hover:text-foreground transition-colors"
                title="Go back"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-12">
            <div class="text-muted-foreground">Loading notifications...</div>
        </div>

        <!-- Empty State -->
        <div v-else-if="notifications.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
            <svg class="h-12 w-12 text-muted-foreground mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p class="text-foreground font-medium">No notifications yet</p>
            <p class="text-sm text-muted-foreground mt-1">Check back later for updates</p>
        </div>

        <!-- Notifications List -->
        <div v-else class="space-y-3">
            <div
                v-for="notification in notifications"
                :key="`${notification.type}-${notification.id}`"
                :class="[
                    'p-4 border border-sidebar-border/70 rounded-lg hover:bg-accent/5 transition-colors group',
                    notification.read ? 'bg-card' : 'bg-accent/10 border-l-4',
                    notification.type === 'announcement' ? 'border-l-amber-500' : '',
                    notification.type === 'community_post' ? 'border-l-blue-500' : ''
                ]"
            >
                <div class="flex gap-4">
                    <!-- Icon -->
                    <div class="text-2xl flex-shrink-0">{{ notification.icon }}</div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <p class="text-sm font-semibold text-foreground">{{ notification.title }}</p>
                                <p v-if="notification.type === 'announcement'" class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                    📢 Announcement
                                </p>
                                <p v-else-if="notification.type === 'community_post'" class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                    💬 Community Post
                                </p>
                            </div>
                            <div v-if="!notification.read" class="h-2 w-2 bg-blue-500 rounded-full flex-shrink-0 mt-1.5"></div>
                        </div>

                        <!-- Message -->
                        <p class="text-sm text-muted-foreground break-words">{{ notification.message }}</p>

                        <!-- Timestamp -->
                        <p class="text-xs text-muted-foreground mt-2">{{ notification.timestamp }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button
                            v-if="!notification.read"
                            @click="handleMarkAsRead(notification.id)"
                            class="text-muted-foreground hover:text-foreground transition-colors p-1"
                            title="Mark as read"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <button
                            @click="handleDelete(notification.id)"
                            class="opacity-0 group-hover:opacity-100 text-muted-foreground hover:text-foreground transition-all p-1"
                            title="Delete notification"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
