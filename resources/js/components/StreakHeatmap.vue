<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    loginDates: string[]; // Array of ISO date strings (e.g., "2024-01-15")
    year?: number;
}

const props = withDefaults(defineProps<Props>(), {
    year: new Date().getFullYear(),
});

const activityMap = computed(() => {
    const map = new Map<string, boolean>();
    props.loginDates.forEach((date) => {
        const normalized = date.split('T')[0]; // Normalize to date-only string
        map.set(normalized, true);
    });
    return map;
});

// Generate all days in the year
const daysInYear = computed(() => {
    const days: Date[] = [];
    const startDate = new Date(props.year, 0, 1);
    const endDate = new Date(props.year, 11, 31);

    let currentDate = new Date(startDate);
    while (currentDate <= endDate) {
        days.push(new Date(currentDate));
        currentDate.setDate(currentDate.getDate() + 1);
    }
    return days;
});

// Organize days into weeks
const weeks = computed(() => {
    const weeksArray: (Date | null)[][] = [];
    let currentWeek: (Date | null)[] = new Array(7).fill(null);
    let weekIndex = 0;

    daysInYear.value.forEach((day) => {
        const dayOfWeek = day.getDay();

        if (dayOfWeek === 0 && weekIndex > 0) {
            // Start of new week (Sunday)
            weeksArray.push(currentWeek);
            currentWeek = new Array(7).fill(null);
            weekIndex = 0;
        }

        currentWeek[dayOfWeek] = day;
        weekIndex++;
    });

    if (currentWeek.some((day) => day !== null)) {
        weeksArray.push(currentWeek);
    }

    return weeksArray;
});

const formatDate = (date: Date): string => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const getActivityLevel = (date: Date | null): 'none' | 'high' => {
    if (!date) return 'none';
    const dateString = formatDate(date);
    return activityMap.value.has(dateString) ? 'high' : 'none';
};

const getActivityColor = (level: string): string => {
    const colors = {
        none: 'bg-gray-100 dark:bg-gray-800',
        high: 'bg-orange-500 dark:bg-orange-600',
    };
    return colors[level as keyof typeof colors] || colors.none;
};

const totalDays = computed(() => activityMap.value.size);

const streak = computed(() => {
    let currentStreak = 0;
    let maxStreak = 0;
    let lastDate: Date | null = null;

    daysInYear.value.forEach((day) => {
        const dateString = formatDate(day);
        const hasActivity = activityMap.value.has(dateString);

        if (hasActivity) {
            if (
                !lastDate ||
                (day.getTime() - lastDate.getTime() === 86400000) // 1 day in ms
            ) {
                currentStreak++;
            } else {
                currentStreak = 1;
            }
            maxStreak = Math.max(maxStreak, currentStreak);
            lastDate = new Date(day);
        } else {
            currentStreak = 0;
        }
    });

    return maxStreak;
});

const formatDateDisplay = (date: Date): string => {
    const monthNames = [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
    ];
    return `${monthNames[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-muted-foreground">{{ props.year }} Activity</h3>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-muted-foreground">{{ totalDays }} days</span>
                <span v-if="streak > 0" class="font-semibold">
                    <span class="text-orange-500">🔥</span>
                    {{ streak }} day max streak
                </span>
            </div>
        </div>

        <!-- Heatmap Container -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
            <div class="flex gap-0.5">
                <!-- Weeks -->
                <div v-for="(week, weekIndex) in weeks" :key="weekIndex" class="flex flex-col gap-0.5">
                    <!-- Days in week -->
                    <div
                        v-for="(day, dayIndex) in week"
                        :key="`${weekIndex}-${dayIndex}`"
                        :class="[
                            'h-3 w-3 rounded-sm transition-all hover:ring-2 hover:ring-offset-1 hover:ring-orange-500 dark:hover:ring-offset-gray-900',
                            getActivityColor(getActivityLevel(day)),
                        ]"
                        :title="day ? formatDateDisplay(day) : ''"
                    />
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
            <div class="flex items-center gap-1">
                <span>No activity</span>
                <div class="h-3 w-3 rounded-sm bg-gray-100 dark:bg-gray-800" />
                <span>High activity</span>
                <div class="h-3 w-3 rounded-sm bg-orange-500 dark:bg-orange-600" />
            </div>
        </div>

        <!-- Motivation message -->
        <div v-if="totalDays === 0" class="rounded-lg bg-gray-50 p-3 text-center dark:bg-gray-800">
            <p class="text-sm text-muted-foreground">Start logging in daily to build your activity heatmap!</p>
        </div>
        <div v-else-if="streak >= 30" class="rounded-lg bg-orange-50 p-3 text-center dark:bg-orange-950">
            <p class="text-sm font-medium text-orange-700 dark:text-orange-300">
                🎉 {{ streak }} day streak! You're unstoppable!
            </p>
        </div>
    </div>
</template>
