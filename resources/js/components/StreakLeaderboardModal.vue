<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface StreakUser {
    id: number;
    name: string;
    email: string;
    currentStreak: number;
    longestStreak: number;
    lastLoginDate: string | null;
    profilePhoto: string;
}

interface Props {
    open: boolean;
}

interface Emit {
    (e: 'update:open', value: boolean): void;
}

const props = defineProps<Props>();
defineEmits<Emit>();

const activeTab = ref<'current' | 'longest'>('current');
const currentStreakUsers = ref<StreakUser[]>([]);
const longestStreakUsers = ref<StreakUser[]>([]);
const isLoading = ref(false);
const error = ref<string | null>(null);

const fetchStreakLeaderboard = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        console.log('Fetching streak leaderboard...');
        const response = await axios.get('/api/streaks/leaderboard');
        console.log('Response:', response.data);
        currentStreakUsers.value = response.data.currentStreak || [];
        longestStreakUsers.value = response.data.longestStreak || [];
    } catch (err) {
        console.error('Failed to fetch streak leaderboard:', err);
        error.value = 'Failed to load streak data';
    } finally {
        isLoading.value = false;
    }
};

// Watch for modal open
watch(() => props.open, (newVal) => {
    if (newVal) {
        fetchStreakLeaderboard();
    }
});

onMounted(() => {
    if (props.open) {
        fetchStreakLeaderboard();
    }
});
</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="max-w-2xl max-h-[85vh] flex flex-col gap-0 p-0">
            <DialogHeader class="border-b border-sidebar-border/70 px-6 py-4">
                <DialogTitle class="flex items-center gap-2">
                    <span class="text-2xl">🔥</span>
                    Streak Leaderboard
                </DialogTitle>
            </DialogHeader>

            <!-- Tabs -->
            <div class="flex gap-2 border-b border-sidebar-border/70 px-6 shrink-0">
                <button
                    @click="activeTab = 'current'"
                    :class="[
                        'px-4 py-2 text-sm font-medium transition-colors',
                        activeTab === 'current'
                            ? 'border-b-2 border-accent text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                >
                    Current Streak
                </button>
                <button
                    @click="activeTab = 'longest'"
                    :class="[
                        'px-4 py-2 text-sm font-medium transition-colors',
                        activeTab === 'longest'
                            ? 'border-b-2 border-accent text-foreground'
                            : 'text-muted-foreground hover:text-foreground'
                    ]"
                >
                    Longest Streak
                </button>
            </div>

            <!-- Content - Scrollable -->
            <div class="flex-1 overflow-y-auto px-6">
                <div v-if="isLoading" class="flex items-center justify-center py-8">
                    <div class="text-sm text-muted-foreground">Loading streak data...</div>
                </div>

                <div v-else-if="error" class="flex items-center justify-center py-8">
                    <div class="text-sm text-red-500">{{ error }}</div>
                </div>

                <!-- Current Streak Tab -->
                <div v-else-if="activeTab === 'current'" class="divide-y divide-sidebar-border/30 py-2">
                    <div
                        v-for="(user, index) in currentStreakUsers"
                        :key="user.id"
                        class="flex items-center gap-4 px-2 py-3 hover:bg-accent/5 transition-colors rounded"
                    >
                        <!-- Profile Picture -->
                        <img 
                            :src="user.profilePhoto" 
                            :alt="user.name"
                            class="w-12 h-12 rounded-full object-cover flex-shrink-0 border border-accent/20"
                        />

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-foreground truncate">{{ user.name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                        </div>

                        <!-- Streak -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-2xl font-bold text-yellow-500">{{ user.currentStreak }}</span>
                            <span class="text-xs text-muted-foreground">days</span>
                        </div>
                    </div>

                    <div v-if="currentStreakUsers.length === 0" class="p-8 text-center text-sm text-muted-foreground">
                        No users found
                    </div>
                </div>

                <!-- Longest Streak Tab -->
                <div v-else-if="activeTab === 'longest'" class="divide-y divide-sidebar-border/30 py-2">
                    <div
                        v-for="(user, index) in longestStreakUsers"
                        :key="user.id"
                        class="flex items-center gap-4 px-2 py-3 hover:bg-accent/5 transition-colors rounded"
                    >
                        <!-- Medal with Profile Picture -->
                        <div class="relative flex-shrink-0">
                            <img 
                                :src="user.profilePhoto" 
                                :alt="user.name"
                                class="w-12 h-12 rounded-full object-cover border border-accent/20"
                            />
                            <div v-if="index < 3" class="absolute -top-2 -right-2 text-xl">
                                {{ index === 0 ? '🥇' : index === 1 ? '🥈' : '🥉' }}
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-foreground truncate">{{ user.name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                        </div>

                        <!-- Streak -->
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ user.longestStreak }}</span>
                            <span class="text-xs text-muted-foreground">days</span>
                        </div>
                    </div>

                    <div v-if="longestStreakUsers.length === 0" class="p-8 text-center text-sm text-muted-foreground">
                        No users found
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
