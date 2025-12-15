<script setup lang="ts">
import { computed } from 'vue';
import StreakFlameIcon from '@/components/StreakFlameIcon.vue';
import StreakMilestoneBadges from '@/components/StreakMilestoneBadges.vue';
import StreakHeatmap from '@/components/StreakHeatmap.vue';
import DontBreakTheChain from '@/components/DontBreakTheChain.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';

interface StreakData {
    currentStreak: number;
    longestStreak: number;
    lastLoginDate: string | null;
    loginDates?: string[]; // Array of login dates for heatmap
}

interface Props {
    streak: StreakData;
    showHeatmap?: boolean;
    showMilestones?: boolean;
    showDontBreakTheChain?: boolean;
    showDetailed?: boolean;
    compact?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showHeatmap: true,
    showMilestones: true,
    showDontBreakTheChain: true,
    showDetailed: true,
    compact: false,
});

const yearForHeatmap = computed(() => {
    const now = new Date();
    return now.getFullYear();
});
</script>

<template>
    <div class="space-y-6">
        <!-- Main Streak Display -->
        <Card v-if="showDontBreakTheChain" class="overflow-hidden">
            <DontBreakTheChain
                :current-streak="streak.currentStreak"
                :longest-streak="streak.longestStreak"
                :last-login-date="streak.lastLoginDate"
            />
        </Card>

        <!-- Grid layout for other components -->
        <div v-if="showDetailed && !compact" class="grid gap-6 md:grid-cols-2">
            <!-- Milestones -->
            <Card v-if="showMilestones">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <span>🎯</span>
                        <span>Milestones</span>
                    </CardTitle>
                    <CardDescription>Track your achievement progress</CardDescription>
                </CardHeader>
                <CardContent>
                    <StreakMilestoneBadges :current-streak="streak.currentStreak" size="md" />
                </CardContent>
            </Card>

            <!-- Stats -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <span>📊</span>
                        <span>Stats</span>
                    </CardTitle>
                    <CardDescription>Your streak performance</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <!-- Current Streak -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-muted-foreground">Current Streak</span>
                                <StreakFlameIcon :streak="streak.currentStreak" size="md" />
                            </div>
                            <p class="text-2xl font-bold">
                                {{ streak.currentStreak }}
                                <span class="text-sm font-normal text-muted-foreground">days</span>
                            </p>
                        </div>

                        <!-- Longest Streak -->
                        <div class="space-y-2">
                            <span class="text-sm font-medium text-muted-foreground">Personal Best</span>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ streak.longestStreak }}
                                <span class="text-sm font-normal text-muted-foreground">days</span>
                            </p>
                        </div>

                        <!-- Last Login -->
                        <div v-if="streak.lastLoginDate" class="space-y-2">
                            <span class="text-sm font-medium text-muted-foreground">Last Login</span>
                            <p class="text-sm">{{ new Date(streak.lastLoginDate).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Heatmap (full width) -->
        <Card v-if="showHeatmap && streak.loginDates">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <span>📅</span>
                    <span>Activity Heatmap</span>
                </CardTitle>
                <CardDescription>Your login activity throughout the year</CardDescription>
            </CardHeader>
            <CardContent>
                <StreakHeatmap :login-dates="streak.loginDates" :year="yearForHeatmap" />
            </CardContent>
        </Card>

        <!-- Compact Summary (for limited space) -->
        <div v-if="compact" class="grid gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <StreakFlameIcon :streak="streak.currentStreak" size="lg" />
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Current Streak</p>
                            <p class="text-2xl font-bold">{{ streak.currentStreak }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-muted-foreground">Personal Best</p>
                        <p class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ streak.longestStreak }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
