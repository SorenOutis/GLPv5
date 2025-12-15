<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import Button from '@/components/ui/button/Button.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';
import { useNotifications } from '@/composables/useNotifications';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { appearance, updateAppearance } = useAppearance();
const { notifications, unreadCount, fetchNotifications, setupEventStream, markAsRead, markAllAsRead, deleteNotification } = useNotifications();
const currentDateTime = ref<string>('');
const userStatus = ref<string>('online');
const isNotificationOpen = ref<boolean>(false);

const updateDateTime = () => {
    const now = new Date();
    const formattedDate = now.toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
    });
    const formattedTime = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
    currentDateTime.value = `${formattedDate}, ${formattedTime}`;
};

onMounted(() => {
    updateDateTime();
    const dateInterval = setInterval(updateDateTime, 60000); // Update every minute
    
    // Fetch initial notifications
    fetchNotifications();
    
    // Setup real-time event stream for live notifications
    setupEventStream();
    
    onUnmounted(() => {
        clearInterval(dateInterval);
    });
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center gap-4">
             <!-- Status and DateTime -->
             <div class="flex items-center gap-2 text-sm text-muted-foreground">
                 <div class="flex items-center gap-1">
                     <div class="h-2 w-2 rounded-full bg-green-500"></div>
                     <span class="capitalize">{{ userStatus }}</span>
                 </div>
                 <span class="text-xs">•</span>
                 <span>{{ currentDateTime }}</span>
             </div>

            <div class="flex items-center gap-2 relative">
                <!-- Notifications Button -->
                <div class="relative inline-flex">
                    <Button 
                        variant="ghost" 
                        size="icon" 
                        title="Notifications"
                        @click="isNotificationOpen = !isNotificationOpen"
                        :class="{ 'bg-accent/10': isNotificationOpen }"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                        <span class="sr-only">Notifications</span>
                    </Button>
                    <!-- Unread badge with count -->
                    <div v-if="unreadCount > 0" class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 rounded-full flex items-center justify-center text-white text-xs font-bold pointer-events-none">
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </div>
                </div>

                <!-- Notifications Dropdown -->
                <div v-if="isNotificationOpen" class="absolute top-full right-0 mt-2 w-80 bg-card border border-sidebar-border/70 rounded-lg shadow-lg z-50">
                    <!-- Header -->
                    <div class="p-4 border-b border-sidebar-border/70 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-sm">Notifications</h3>
                            <p v-if="unreadCount > 0" class="text-xs text-muted-foreground">{{ unreadCount }} unread</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button 
                                v-if="unreadCount > 0"
                                @click="markAllAsRead"
                                class="text-xs font-medium text-muted-foreground hover:text-foreground transition-colors"
                                title="Mark all as read"
                            >
                                Mark all read
                            </button>
                            <button 
                                @click="isNotificationOpen = false"
                                class="text-muted-foreground hover:text-foreground transition-colors"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Notifications List -->
                    <div v-if="notifications.length === 0" class="p-4 text-center text-sm text-muted-foreground">
                        No notifications yet
                    </div>
                    <div v-else class="max-h-96 overflow-y-auto overflow-x-hidden">
                        <div 
                            v-for="notification in notifications" 
                            :key="`${notification.type}-${notification.id}`"
                            :class="[
                                'p-3 border-b border-sidebar-border/30 last:border-b-0 hover:bg-accent/5 cursor-pointer transition-colors group',
                                notification.read ? '' : 'bg-accent/10',
                                notification.type === 'announcement' ? 'border-l-4 border-l-amber-500' : '',
                                notification.type === 'community_post' ? 'border-l-4 border-l-blue-500' : ''
                            ]"
                            @click="markAsRead(notification.id)"
                        >
                            <div class="flex gap-3">
                                <div class="text-xl flex-shrink-0">{{ notification.icon }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-foreground">{{ notification.title }}</p>
                                            <p v-if="notification.type === 'announcement'" class="text-xs text-amber-600 dark:text-amber-400">Announcement</p>
                                            <p v-else-if="notification.type === 'community_post'" class="text-xs text-blue-600 dark:text-blue-400">Community</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div v-if="!notification.read" class="h-2 w-2 bg-blue-500 rounded-full mt-1 flex-shrink-0"></div>
                                            <button
                                                @click.stop="deleteNotification(notification.id)"
                                                class="opacity-0 group-hover:opacity-100 text-muted-foreground hover:text-foreground transition-all"
                                                title="Delete notification"
                                            >
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted-foreground mt-1 break-words">{{ notification.message }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">{{ notification.timestamp }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-3 border-t border-sidebar-border/70 text-center">
                        <button 
                            @click="() => { isNotificationOpen = false; router.push('/notifications'); }"
                            class="text-xs text-primary hover:underline transition-colors font-medium"
                        >
                            View all notifications
                        </button>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon">
                            <svg
                                v-if="appearance === 'light'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            <svg
                                v-else-if="appearance === 'dark'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                            <span class="sr-only">Toggle theme</span>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuRadioGroup :model-value="appearance" @update:model-value="updateAppearance">
                            <DropdownMenuRadioItem value="light">
                                Light
                            </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="dark">
                                Dark
                            </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem value="system">
                                System
                            </DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
         </div>
    </header>
</template>
