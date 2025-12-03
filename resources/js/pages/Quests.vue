<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Progress from '@/components/ui/progress/Progress.vue';
import StreakCard from '@/components/StreakCard.vue';

interface Quest {
    id: number;
    title: string;
    description: string;
    objective: string;
    reward: number;
    difficulty: 'Easy' | 'Medium' | 'Hard' | 'Legendary';
    progress: number;
    status: 'active' | 'completed' | 'failed' | 'available';
}

interface StreakData {
    currentStreak: number;
    longestStreak: number;
    lastLoginDate: string | null;
}

interface Props {
    activeQuests: Quest[];
    completedQuests: Quest[];
    stats: {
        totalActive: number;
        totalCompleted: number;
        totalRewards: number;
        streakDays: number;
    };
    streak?: StreakData;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Quests',
        href: '#',
    },
];

const selectedFilter = ref<'active' | 'completed'>('active');

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Easy': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Medium': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Hard': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'Legendary': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800';
};

const getDifficultyIcon = (difficulty: string) => {
    const icons: Record<string, string> = {
        'Easy': '⭐',
        'Medium': '⭐⭐',
        'Hard': '⭐⭐⭐',
        'Legendary': '👑',
    };
    return icons[difficulty] || '⭐';
};

const getFilteredQuests = () => {
    switch (selectedFilter.value) {
        case 'completed':
            return props.completedQuests;
        default:
            return props.activeQuests;
    }
};
</script>

<template>

    <Head title="Quests" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Stats Section -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Active</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalActive }}</div>
                        <p class="text-xs text-muted-foreground mt-1">In progress</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Completed</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCompleted }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Finished</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Rewards</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalRewards }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Total XP</p>
                    </CardContent>
                </Card>

                <StreakCard :streak="streak" />
            </div>

            <!-- Filter Tabs -->
            <div class="flex gap-2 border-b border-sidebar-border/30">
                <button v-for="filter in ['active', 'completed'] as const" :key="filter"
                    @click="selectedFilter = filter" :class="[
                        'px-4 py-2 text-sm font-medium transition-colors capitalize',
                        selectedFilter === filter
                            ? 'text-accent border-b-2 border-accent'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                    {{ filter }} ({{ filter === 'active' ? activeQuests.length : completedQuests.length }})
                </button>
            </div>

            <!-- Quests List -->
            <div class="space-y-3">
                <div v-for="quest in getFilteredQuests()" :key="quest.id">
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border hover:shadow-md transition-shadow">
                        <CardContent class="pt-6">
                            <div class="flex items-start gap-4">
                                <!-- Icon -->
                                <div class="text-3xl mt-1">
                                    {{ getDifficultyIcon(quest.difficulty) }}
                                </div>

                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ quest.title }}</h3>
                                            <p class="text-sm text-muted-foreground">{{ quest.objective }}</p>
                                        </div>
                                        <span
                                            :class="['px-2 py-1 text-xs rounded-full font-medium', getDifficultyColor(quest.difficulty)]">
                                            {{ quest.difficulty }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-muted-foreground mb-4">{{ quest.description }}</p>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-medium">Progress</span>
                                            <span class="text-xs text-muted-foreground">{{ quest.progress }}%</span>
                                        </div>
                                        <Progress :value="quest.progress" class="h-2" />
                                        <div class="flex items-center justify-between text-xs text-muted-foreground">
                                            <span>0</span>
                                            <span>100</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 flex-wrap">
                                        <div class="flex items-center gap-1">
                                            <span class="text-yellow-500">⭐</span>
                                            <span class="text-sm font-medium">{{ quest.reward }} XP</span>
                                        </div>
                                        <div v-if="selectedFilter === 'completed'"
                                            class="text-sm text-green-600 dark:text-green-400">
                                            ✓ Completed
                                        </div>

                                        <span v-if="selectedFilter === 'completed'"
                                            class="px-3 py-1 text-xs rounded-full font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            ✓ Completed
                                        </span>
                                        <span v-else
                                            class="px-3 py-1 text-xs rounded-full font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            In Progress
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="getFilteredQuests().length === 0" class="text-center py-12">
                <p class="text-muted-foreground mb-4">No {{ selectedFilter }} quests</p>
            </div>
        </div>
    </AppLayout>
</template>
