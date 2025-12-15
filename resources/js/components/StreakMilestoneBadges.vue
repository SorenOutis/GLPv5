<script setup lang="ts">
import { computed } from 'vue';

interface Milestone {
    days: number;
    label: string;
    icon: string;
    color: string;
}

interface Props {
    currentStreak: number;
    size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
});

const milestones: Milestone[] = [
    { days: 7, label: '7 Days', icon: '⚡', color: 'bg-yellow-100 dark:bg-yellow-900' },
    { days: 14, label: '2 Weeks', icon: '🌟', color: 'bg-amber-100 dark:bg-amber-900' },
    { days: 30, label: '1 Month', icon: '🔥', color: 'bg-orange-100 dark:bg-orange-900' },
    { days: 60, label: '2 Months', icon: '⭐', color: 'bg-red-100 dark:bg-red-900' },
    { days: 90, label: '3 Months', icon: '💎', color: 'bg-purple-100 dark:bg-purple-900' },
    { days: 180, label: '6 Months', icon: '👑', color: 'bg-indigo-100 dark:bg-indigo-900' },
    { days: 365, label: '1 Year', icon: '🏆', color: 'bg-blue-100 dark:bg-blue-900' },
];

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'h-10 w-10 text-sm',
        md: 'h-12 w-12 text-base',
        lg: 'h-14 w-14 text-lg',
    };
    return sizes[props.size];
});

const unlocked = computed(() => {
    return milestones.filter((m) => props.currentStreak >= m.days);
});

const nextMilestone = computed(() => {
    return milestones.find((m) => props.currentStreak < m.days);
});

const progressToNextMilestone = computed(() => {
    if (!nextMilestone.value) return 100;
    const prev = milestones.find((m) => m.days <= props.currentStreak);
    const prevDays = prev?.days ?? 0;
    const nextDays = nextMilestone.value.days;
    const progress = ((props.currentStreak - prevDays) / (nextDays - prevDays)) * 100;
    return Math.min(Math.max(progress, 0), 100);
});
</script>

<template>
    <div class="space-y-4">
        <!-- Unlocked Milestones -->
        <div v-if="unlocked.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold text-muted-foreground">Achievements Unlocked</h3>
            <div class="flex flex-wrap gap-2">
                <div
                    v-for="milestone in unlocked"
                    :key="milestone.days"
                    :class="[sizeClasses, milestone.color]"
                    class="flex items-center justify-center rounded-lg border-2 border-current font-bold transition-transform hover:scale-110"
                    :title="`${milestone.label} - Day ${milestone.days}`"
                >
                    <span>{{ milestone.icon }}</span>
                </div>
            </div>
        </div>

        <!-- Next Milestone Progress -->
        <div v-if="nextMilestone" class="space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-muted-foreground">
                    Next Goal: {{ nextMilestone.label }}
                </h3>
                <span class="text-xs font-medium text-muted-foreground">
                    {{ currentStreak }}/{{ nextMilestone.days }} days
                </span>
            </div>
            <div class="overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                <div
                    class="h-2 bg-gradient-to-r from-yellow-400 via-amber-400 to-orange-400 transition-all duration-500 ease-out"
                    :style="{ width: `${progressToNextMilestone}%` }"
                />
            </div>
        </div>

        <!-- Celebration message -->
        <div v-if="unlocked.length >= 3" class="rounded-lg bg-purple-50 p-3 text-center dark:bg-purple-950">
            <p class="text-sm font-medium text-purple-700 dark:text-purple-300">
                🎉 You're on fire! {{ unlocked.length }} milestone{{ unlocked.length > 1 ? 's' : '' }}
                unlocked!
            </p>
        </div>
    </div>
</template>
