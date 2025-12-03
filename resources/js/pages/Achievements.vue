<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Achievement {
    id: number;
    name: string;
    description: string;
    icon: string;
    category: string;
    difficulty: 'Easy' | 'Medium' | 'Hard' | 'Legendary';
    unlocked: boolean;
    unlockedAt?: string;
    xp_reward: number;
}

interface Props {
    allAchievements: Achievement[];
    stats: {
        totalUnlocked: number;
        totalAchievements: number;
        completionPercentage: number;
        xpEarned: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Achievements',
        href: '#',
    },
];

const selectedFilter = ref<'all' | 'unlocked' | 'locked'>('all');
const selectedCategory = ref<string | null>(null);
const selectedAchievement = ref<Achievement | null>(null);

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Easy': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Medium': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Hard': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
        'Legendary': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800';
};

const allCategories = computed(() => {
    const categories = new Set(props.allAchievements.map(a => a.category));
    return Array.from(categories).sort();
});

const getFilteredAchievements = computed(() => {
    let filtered = props.allAchievements;

    // Filter by unlock status
    if (selectedFilter.value === 'unlocked') {
        filtered = filtered.filter(a => a.unlocked);
    } else if (selectedFilter.value === 'locked') {
        filtered = filtered.filter(a => !a.unlocked);
    }

    // Filter by category
    if (selectedCategory.value) {
        filtered = filtered.filter(a => a.category === selectedCategory.value);
    }

    // Sort: unlocked first, then by name
    return filtered.sort((a, b) => {
        if (a.unlocked !== b.unlocked) return a.unlocked ? -1 : 1;
        return a.name.localeCompare(b.name);
    });
});

const getCategoryCount = (category: string, filterStatus: 'all' | 'unlocked' | 'locked') => {
    let achievements = props.allAchievements.filter(a => a.category === category);

    if (filterStatus === 'unlocked') {
        achievements = achievements.filter(a => a.unlocked);
    } else if (filterStatus === 'locked') {
        achievements = achievements.filter(a => !a.unlocked);
    }

    return achievements.length;
};
</script>

<template>

    <Head title="Achievements" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="relative h-full flex-1">
            <!-- Blurred Content -->
            <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 blur-sm pointer-events-none">
            <!-- Stats Section -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Unlocked</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalUnlocked }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Achievements</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Total</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalAchievements }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Available</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Completion</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.completionPercentage }}%</div>
                        <p class="text-xs text-muted-foreground mt-1">Progress</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">XP Earned</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.xpEarned }}</div>
                        <p class="text-xs text-muted-foreground mt-1">From achievements</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Filter Tabs -->
            <div class="flex gap-2 border-b border-sidebar-border/30 overflow-x-auto pb-2">
                <button v-for="filter in ['all', 'unlocked', 'locked'] as const" :key="filter"
                    @click="selectedFilter = filter; selectedCategory = null" :class="[
                        'px-4 py-2 text-sm font-medium transition-colors capitalize whitespace-nowrap',
                        selectedFilter === filter
                            ? 'text-accent border-b-2 border-accent'
                            : 'text-muted-foreground hover:text-foreground'
                    ]">
                    {{ filter }} ({{ filter === 'all' ? stats.totalAchievements : filter === 'unlocked' ?
                        stats.totalUnlocked : stats.totalAchievements - stats.totalUnlocked }})
                </button>
            </div>

            <!-- Category Tabs (Dynamic) -->
            <div v-if="allCategories.length > 0"
                class="flex gap-2 border-b border-sidebar-border/30 overflow-x-auto pb-2">
                <button @click="selectedCategory = null" :class="[
                    'px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap',
                    !selectedCategory
                        ? 'text-accent border-b-2 border-accent'
                        : 'text-muted-foreground hover:text-foreground'
                ]">
                    All Categories
                </button>
                <button v-for="category in allCategories" :key="category" @click="selectedCategory = category" :class="[
                    'px-4 py-2 text-sm font-medium transition-colors whitespace-nowrap',
                    selectedCategory === category
                        ? 'text-accent border-b-2 border-accent'
                        : 'text-muted-foreground hover:text-foreground'
                ]">
                    {{ category }} ({{ getCategoryCount(category, selectedFilter) }})
                </button>
            </div>

            <!-- Achievements Grid -->
            <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div v-for="achievement in getFilteredAchievements" :key="achievement.id">
                    <Card :class="[
                        'border-sidebar-border/70 dark:border-sidebar-border h-full hover:shadow-md transition-all duration-150 cursor-pointer',
                        achievement.unlocked
                            ? 'hover:scale-105 hover:shadow-lg'
                            : 'opacity-50 grayscale hover:opacity-75'
                    ]" @click="selectedAchievement = achievement">
                        <CardContent class="pt-6 flex flex-col items-center justify-center text-center h-full">
                            <div class="text-5xl mb-2">{{ achievement.icon }}</div>
                            <h3 class="font-semibold text-sm mb-1">{{ achievement.name }}</h3>
                            <p class="text-xs text-muted-foreground mb-2 line-clamp-2">{{ achievement.description }}</p>

                            <div v-if="achievement.unlocked"
                                class="w-full mt-auto pt-2 border-t border-sidebar-border/30">
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium">
                                    ✓ Unlocked
                                </p>
                                <p class="text-xs text-muted-foreground">{{ achievement.unlockedAt }}</p>
                            </div>

                            <div v-else class="w-full mt-auto pt-2">
                                <span
                                    :class="['text-xs rounded-full px-2 py-1 inline-block', getDifficultyColor(achievement.difficulty)]">
                                    {{ achievement.difficulty }}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="getFilteredAchievements.length === 0" class="text-center py-12">
                <p class="text-muted-foreground">No {{ selectedFilter === 'all' ? '' : selectedFilter }}{{
                    selectedCategory ? ' in ' + selectedCategory : '' }} achievements</p>
            </div>

            <!-- Achievement Details Modal -->
            <Dialog v-if="selectedAchievement" :open="!!selectedAchievement"
                @update:open="(open) => !open && (selectedAchievement = null)">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-3">
                            <span class="text-4xl">{{ selectedAchievement.icon }}</span>
                            {{ selectedAchievement.name }}
                        </DialogTitle>
                        <DialogDescription>
                            {{ selectedAchievement.description }}
                        </DialogDescription>
                    </DialogHeader>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-lg bg-accent/10 border border-accent/20">
                                <p class="text-xs text-muted-foreground mb-1">Difficulty</p>
                                <p :class="['font-bold text-sm', getDifficultyColor(selectedAchievement.difficulty)]">
                                    {{ selectedAchievement.difficulty }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-yellow-500/10 border border-yellow-500/20">
                                <p class="text-xs text-muted-foreground mb-1">XP Reward</p>
                                <p class="font-bold text-sm text-yellow-600 dark:text-yellow-400">
                                    +{{ selectedAchievement.xp_reward }}
                                </p>
                            </div>
                            <div class="p-4 rounded-lg bg-accent/10 border border-accent/20">
                                <p class="text-xs text-muted-foreground mb-1">Category</p>
                                <p class="font-bold text-sm">{{ selectedAchievement.category }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-accent/10 border border-accent/20">
                                <p class="text-xs text-muted-foreground mb-1">Status</p>
                                <p class="font-bold text-sm">
                                    {{ selectedAchievement.unlocked ? '✓ Unlocked' : '🔒 Locked' }}
                                </p>
                            </div>
                        </div>

                        <div v-if="selectedAchievement.unlocked"
                            class="p-4 rounded-lg bg-green-500/10 border border-green-500/20">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                ✓ Unlocked {{ selectedAchievement.unlockedAt }}
                            </p>
                        </div>
                        <div v-else class="p-4 rounded-lg bg-muted border border-sidebar-border/30">
                            <p class="text-sm font-medium text-muted-foreground">
                                Keep working to unlock this achievement!
                            </p>
                        </div>
                    </div>
                    </DialogContent>
                    </Dialog>
                    </div>

                    <!-- Coming Soon Overlay -->
                    <div
                        class="absolute inset-0 flex h-full flex-1 flex-col items-center justify-start pt-16 rounded-xl">
                        <div class="text-center space-y-6 max-w-md">
                    <div class="space-y-2">
                       <h1
                            class="text-5xl font-bold bg-gradient-to-r from-accent to-accent/60 dark:from-yellow-400 dark:to-yellow-500 bg-clip-text text-transparent">
                            Coming Soon
                        </h1>
                       <p class="text-xl text-muted-foreground">
                           Achievements feature is being prepared
                       </p>
                    </div>

                    <div class="flex justify-center">
                       <div class="text-6xl animate-bounce">🏆</div>
                    </div>

                    <p class="text-muted-foreground">
                       We're working hard to bring you an amazing achievements system. Stay tuned for updates!
                    </p>

                    <Button @click="$inertia.visit(dashboard().url)" variant="default" class="w-full">
                       ← Back to Dashboard
                    </Button>
                    </div>
                    </div>
                    </div>
                    </AppLayout>
                    </template>
