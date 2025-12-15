<script setup lang="ts">
import { computed, ref } from 'vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import StreakLeaderboardModal from '@/components/StreakLeaderboardModal.vue';
import StreakFlameIcon from '@/components/StreakFlameIcon.vue';

interface StreakData {
    currentStreak: number;
    longestStreak: number;
    lastLoginDate: string | null;
}

interface Props {
    streak?: StreakData;
    compact?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    compact: false,
});

const isStreakCardHovered = ref(false);
const isLeaderboardModalOpen = ref(false);

const currentStreak = computed(() => props.streak?.currentStreak ?? 0);
const longestStreak = computed(() => props.streak?.longestStreak ?? 0);

const streakColor = computed(() => {
    if (currentStreak.value >= 30) return 'text-orange-600 dark:text-orange-400';
    if (currentStreak.value >= 14) return 'text-amber-500 dark:text-amber-400';
    if (currentStreak.value >= 7) return 'text-yellow-500 dark:text-yellow-400';
    return 'text-gray-400 dark:text-gray-600';
});

const streakBgColor = computed(() => {
    if (currentStreak.value >= 30) return 'bg-orange-50 dark:bg-orange-950';
    if (currentStreak.value >= 14) return 'bg-amber-50 dark:bg-amber-950';
    if (currentStreak.value >= 7) return 'bg-yellow-50 dark:bg-yellow-950';
    return 'bg-gray-50 dark:bg-gray-900';
});

const streakMessage = computed(() => {
    if (currentStreak.value === 0) return 'Start your streak today!';
    if (currentStreak.value === 1) return 'Day 1 - Keep it going!';
    if (currentStreak.value < 7) return `${currentStreak.value} day streak - Almost a week!`;
    if (currentStreak.value < 14) return `${currentStreak.value} day streak - Great job!`;
    if (currentStreak.value < 30) return `${currentStreak.value} day streak - Excellent work!`;
    return `${currentStreak.value} day streak - LEGENDARY!`;
});
</script>

<template>
    <StreakLeaderboardModal 
        :open="isLeaderboardModalOpen"
        @update:open="isLeaderboardModalOpen = $event"
    />
    
    <Card :class="streakBgColor"
        class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105 overflow-hidden"
        @mouseenter="isStreakCardHovered = true" @mouseleave="isStreakCardHovered = false"
        @click="isLeaderboardModalOpen = true">
        <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
            <CardTitle class="text-sm font-medium flex items-center gap-2">
                <StreakFlameIcon :streak="currentStreak" size="sm" />
                <span>Streak</span>
            </CardTitle>
            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 18.657L13.414 22.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </CardHeader>

        <CardContent>
            <!-- Compact View (default) -->
            <div class="transition-all duration-300" :style="{
                maxHeight: isStreakCardHovered ? '0px' : '120px',
                opacity: isStreakCardHovered ? 0 : 1
            }">
                <div class="text-3xl font-bold" :class="streakColor">{{ currentStreak }}</div>
                <p class="text-xs text-muted-foreground mt-2">consecutive days</p>
            </div>

            <!-- Detailed View (on hover) -->
            <div class="space-y-3 overflow-hidden transition-all duration-300 ease-out" :style="{
                maxHeight: isStreakCardHovered ? '200px' : '0px',
                opacity: isStreakCardHovered ? 1 : 0
            }">
                <!-- Current Streak -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-muted-foreground">Current Streak</p>
                    <p :class="streakColor" class="text-2xl font-bold">
                        {{ currentStreak }}
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">days</span>
                    </p>
                </div>

                <!-- Longest Streak -->
                <div class="space-y-1">
                    <p class="text-xs font-medium text-muted-foreground">Longest Streak</p>
                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">
                        {{ longestStreak }}
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">days</span>
                    </p>
                </div>


            </div>
        </CardContent>
    </Card>
</template>
