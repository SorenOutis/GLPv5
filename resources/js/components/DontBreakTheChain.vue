<script setup lang="ts">
import { computed } from 'vue';
import StreakFlameIcon from '@/components/StreakFlameIcon.vue';

interface Props {
    currentStreak: number;
    longestStreak: number;
    lastLoginDate?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    lastLoginDate: null,
});

const daysSinceLogin = computed(() => {
    if (!props.lastLoginDate) return null;
    const last = new Date(props.lastLoginDate);
    const now = new Date();
    const days = Math.floor((now.getTime() - last.getTime()) / (1000 * 60 * 60 * 24));
    return days;
});

const isAtRisk = computed(() => {
    return daysSinceLogin.value === 1; // Haven't logged in today
});

const chainBroken = computed(() => {
    return daysSinceLogin.value! > 1 && props.currentStreak === 0; // More than 1 day and streak is 0
});

const chainActive = computed(() => {
    return daysSinceLogin.value === 0; // Logged in today
});

const motivationalMessage = computed(() => {
    if (props.currentStreak === 0) {
        return 'Start fresh today! Build your first streak.';
    }
    if (chainActive.value) {
        return `Keep the chain alive! ${props.currentStreak} day${props.currentStreak > 1 ? 's' : ''}`;
    }
    if (isAtRisk.value) {
        return `Come back today! Your ${props.currentStreak} day streak is at risk!`;
    }
    return `Don't break the chain! Return to build your next streak.`;
});

const chainColor = computed(() => {
    if (props.currentStreak >= 30) return 'text-orange-600 dark:text-orange-400';
    if (props.currentStreak >= 14) return 'text-amber-500 dark:text-amber-400';
    if (props.currentStreak >= 7) return 'text-yellow-500 dark:text-yellow-400';
    return 'text-gray-400 dark:text-gray-600';
});

const chainBackground = computed(() => {
    if (chainActive.value) {
        if (props.currentStreak >= 30) return 'bg-orange-50 dark:bg-orange-950';
        if (props.currentStreak >= 14) return 'bg-amber-50 dark:bg-amber-950';
        if (props.currentStreak >= 7) return 'bg-yellow-50 dark:bg-yellow-950';
        return 'bg-blue-50 dark:bg-blue-950';
    }
    if (chainBroken.value) {
        return 'bg-red-50 dark:bg-red-950';
    }
    if (isAtRisk.value) {
        return 'bg-orange-50 dark:bg-orange-950';
    }
    return 'bg-gray-50 dark:bg-gray-900';
});

const chainLinks = computed(() => {
    // Visual representation of the chain (max 7 links for current week)
    const links = [];
    const daysInWeek = 7;

    for (let i = 0; i < daysInWeek; i++) {
        const daysBack = daysInWeek - 1 - i;
        const isLinked = daysBack < props.currentStreak;
        const isToday = daysBack === 0;

        links.push({
            index: i,
            linked: isLinked,
            today: isToday,
        });
    }

    return links;
});
</script>

<template>
    <div :class="[chainBackground, 'rounded-lg border-2 border-current p-6 transition-all']">
        <!-- Header with icon -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <StreakFlameIcon :streak="currentStreak" size="lg" :animated="chainActive" />
                <div>
                    <h3 class="font-bold" :class="chainColor">
                        {{ currentStreak }} Day Streak
                    </h3>
                    <p class="text-sm text-muted-foreground">{{ motivationalMessage }}</p>
                </div>
            </div>

            <!-- Longest streak badge -->
            <div v-if="longestStreak > 0" class="text-right">
                <p class="text-xs text-muted-foreground">Personal Best</p>
                <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ longestStreak }}</p>
            </div>
        </div>

        <!-- Visual chain -->
        <div class="mt-4 space-y-2">
            <p class="text-xs font-semibold text-muted-foreground">This Week</p>
            <div class="flex gap-2">
                <div
                    v-for="link in chainLinks"
                    :key="link.index"
                    :class="[
                        'flex h-10 flex-1 items-center justify-center rounded-lg font-bold transition-all',
                        link.linked
                            ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white shadow-lg'
                            : 'border-2 border-dashed border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-600',
                        link.today && 'ring-2 ring-orange-500 ring-offset-2 dark:ring-offset-gray-900',
                    ]"
                    :title="`Day ${link.index + 1}${link.today ? ' (Today)' : ''}`"
                >
                    <span v-if="link.linked" class="text-lg">⛓️</span>
                    <span v-else class="text-xs">{{ link.index + 1 }}</span>
                </div>
            </div>
        </div>

        <!-- Status indicators -->
        <div class="mt-4 flex gap-2">
            <div v-if="chainActive" class="flex items-center gap-2 text-sm font-medium text-green-600 dark:text-green-400">
                <span class="h-2 w-2 rounded-full bg-green-600 dark:bg-green-400 animate-pulse" />
                Chain Active
            </div>
            <div v-else-if="isAtRisk" class="flex items-center gap-2 text-sm font-medium text-orange-600 dark:text-orange-400">
                <span class="h-2 w-2 rounded-full bg-orange-600 dark:bg-orange-400 animate-pulse" />
                At Risk - Log in today!
            </div>
            <div v-else-if="chainBroken" class="flex items-center gap-2 text-sm font-medium text-red-600 dark:text-red-400">
                <span class="h-2 w-2 rounded-full bg-red-600 dark:bg-red-400" />
                Chain Broken
            </div>
        </div>

        <!-- Motivational quote -->
        <div v-if="currentStreak > 0" class="mt-4 border-t border-current/20 pt-3">
            <p class="text-center text-xs italic text-muted-foreground">
                "The secret of success is doing the unusual. Do it again and again." — Jerry Weintraub
            </p>
        </div>
    </div>
</template>
