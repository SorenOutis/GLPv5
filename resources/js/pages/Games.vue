<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Progress from '@/components/ui/progress/Progress.vue';
import SkeletonStats from '@/components/SkeletonStats.vue';
import SkeletonCard from '@/components/SkeletonCard.vue';
import { Star, Play, Lock, CheckCircle2, Clock, Users } from 'lucide-vue-next';

interface Game {
    id: number;
    name: string;
    description: string;
    category: string;
    difficulty: 'Beginner' | 'Intermediate' | 'Advanced';
    players: number;
    rating: number;
    thumbnail: string;
    status: 'playing' | 'completed' | 'not_started';
    progress: number;
    timeSpent: string;
    badge: string;
}

interface Props {
    games: Game[];
    stats: {
        totalGames: number;
        gamesPlaying: number;
        gamesCompleted: number;
        averageRating: number;
        totalTimePlayed: string;
        averageProgress: number;
    };
}

const props = defineProps<Props>();
const page = usePage();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Games',
        href: '#',
    },
];

const selectedFilter = ref<'all' | 'playing' | 'completed' | 'available'>('all');
const selectedCategory = ref<string>('All');
const isLoading = computed(() => page.props.loading === true);

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Beginner': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Intermediate': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Advanced': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800';
};

const getDifficultyGradient = (difficulty: string) => {
    const gradients: Record<string, string> = {
        'Beginner': 'from-green-50 to-emerald-50 dark:from-green-950 dark:to-emerald-950',
        'Intermediate': 'from-yellow-50 to-amber-50 dark:from-yellow-950 dark:to-amber-950',
        'Advanced': 'from-red-50 to-rose-50 dark:from-red-950 dark:to-rose-950',
    };
    return gradients[difficulty] || 'from-gray-50 to-slate-50';
};

const getStatusBadgeColor = (status: string) => {
    const colors: Record<string, string> = {
        'playing': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'not_started': 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        'playing': 'Playing',
        'completed': 'Completed',
        'not_started': 'Not Started',
    };
    return labels[status] || status;
};

const getCategories = () => {
    const categories = new Set(props.games.map(g => g.category));
    return ['All', ...Array.from(categories).sort()];
};

const getFilteredGames = () => {
    let filtered = [...props.games];

    // Filter by status
    if (selectedFilter.value !== 'all') {
        filtered = filtered.filter(g => g.status === selectedFilter.value);
    }

    // Filter by category
    if (selectedCategory.value !== 'All') {
        filtered = filtered.filter(g => g.category === selectedCategory.value);
    }

    return filtered;
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'playing':
            return Play;
        case 'completed':
            return CheckCircle2;
        default:
            return Lock;
    }
};
</script>

<template>
    <Head title="Games" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 px-4 sm:px-0">
            <!-- Header Section -->
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ props.games.length }} Games
                </h1>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                    Play interactive games and earn XP while learning
                </p>
            </div>

            <!-- Stats Cards -->
            <div v-if="!isLoading" class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-6">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Total Games
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ props.stats.totalGames }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Playing
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ props.stats.gamesPlaying }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Completed
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ props.stats.gamesCompleted }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Avg Rating
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-1">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">
                                {{ props.stats.averageRating.toFixed(1) }}
                            </span>
                            <Star class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Time Played
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ props.stats.totalTimePlayed }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-slate-600 dark:text-slate-400">
                            Avg Progress
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ props.stats.averageProgress }}%
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-else class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-3 lg:grid-cols-6">
                <SkeletonStats v-for="i in 6" :key="i" />
            </div>

            <!-- Filters -->
            <div class="space-y-3 sm:space-y-4">
                <!-- Status Filter -->
                <div class="space-y-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white">Status</h3>
                    <div class="flex flex-wrap gap-1.5 sm:gap-2">
                        <Button
                            v-for="status in ['all', 'playing', 'completed', 'available']"
                            :key="status"
                            :variant="selectedFilter === status ? 'default' : 'outline'"
                            @click="selectedFilter = status as any"
                            class="capitalize text-xs sm:text-sm px-2 sm:px-3 h-8 sm:h-10"
                        >
                            {{ status === 'all' ? 'All Games' : status.replace('_', ' ') }}
                        </Button>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="space-y-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-slate-900 dark:text-white">Category</h3>
                    <div class="flex flex-wrap gap-1.5 sm:gap-2">
                        <Button
                            v-for="category in getCategories()"
                            :key="category"
                            :variant="selectedCategory === category ? 'default' : 'outline'"
                            @click="selectedCategory = category"
                            class="text-xs sm:text-sm px-2 sm:px-3 h-8 sm:h-10"
                        >
                            {{ category }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Games Grid -->
            <div v-if="!isLoading" class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="game in getFilteredGames()"
                    :key="game.id"
                    class="overflow-hidden hover:shadow-lg transition-shadow duration-200 flex flex-col"
                >
                    <!-- Game Thumbnail -->
                    <div class="relative h-40 sm:h-48 overflow-hidden bg-gradient-to-br" :class="getDifficultyGradient(game.difficulty)">
                        <img
                            :src="game.thumbnail"
                            :alt="game.name"
                            class="h-full w-full object-cover opacity-80"
                        />
                        <div class="absolute inset-0 flex items-end justify-between p-3 sm:p-4 bg-gradient-to-t from-black/60 to-transparent">
                            <span class="text-2xl sm:text-3xl">{{ game.badge }}</span>
                            <div class="flex gap-2">
                                <span
                                    :class="['px-2 sm:px-3 py-1 rounded-full text-xs font-semibold', getDifficultyColor(game.difficulty)]"
                                >
                                    {{ game.difficulty }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Game Content -->
                    <CardHeader class="pb-3 sm:pb-6">
                        <div class="space-y-1 sm:space-y-2">
                            <div class="flex items-start justify-between gap-1 sm:gap-2">
                                <CardTitle class="line-clamp-2 text-base sm:text-lg">{{ game.name }}</CardTitle>
                                <span
                                    :class="['px-2 py-0.5 sm:py-1 rounded-full text-xs font-semibold whitespace-nowrap', getStatusBadgeColor(game.status)]"
                                >
                                    {{ getStatusLabel(game.status) }}
                                </span>
                            </div>
                            <CardDescription class="text-xs sm:text-sm">{{ game.category }}</CardDescription>
                        </div>
                    </CardHeader>

                    <CardContent class="flex-1 space-y-3 sm:space-y-4 px-4 sm:px-6">
                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                            {{ game.description }}
                        </p>

                        <!-- Progress Bar (if playing or completed) -->
                        <div v-if="game.status !== 'not_started'" class="space-y-1 sm:space-y-2">
                            <div class="flex items-center justify-between text-xs sm:text-sm">
                                <span class="text-slate-600 dark:text-slate-400">Progress</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ game.progress }}%</span>
                            </div>
                            <Progress :value="game.progress" class="h-2" />
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 pt-2 sm:pt-3 border-t border-slate-200 dark:border-slate-700">
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-0.5 sm:gap-1 text-slate-600 dark:text-slate-400 mb-0.5 sm:mb-1">
                                    <Star class="h-2.5 w-2.5 sm:h-3 sm:w-3 fill-yellow-400 text-yellow-400" />
                                    <span class="text-xs font-semibold">{{ game.rating }}</span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-500">Rating</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-0.5 sm:gap-1 text-slate-600 dark:text-slate-400 mb-0.5 sm:mb-1">
                                    <Users class="h-2.5 w-2.5 sm:h-3 sm:w-3" />
                                    <span class="text-xs font-semibold">{{ (game.players / 1000).toFixed(1) }}k</span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-500">Players</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center gap-0.5 sm:gap-1 text-slate-600 dark:text-slate-400 mb-0.5 sm:mb-1">
                                    <Clock class="h-2.5 w-2.5 sm:h-3 sm:w-3" />
                                    <span class="text-xs font-semibold">{{ game.timeSpent }}</span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-500">Time</p>
                            </div>
                        </div>
                    </CardContent>

                    <!-- Action Button -->
                    <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                        <Button
                            class="w-full gap-1 sm:gap-2 text-xs sm:text-sm h-8 sm:h-10"
                            :variant="game.status === 'not_started' ? 'outline' : 'default'"
                        >
                            <component :is="getStatusIcon(game.status)" class="h-3 w-3 sm:h-4 sm:w-4" />
                            {{ game.status === 'completed' ? 'Play Again' : game.status === 'playing' ? 'Continue' : 'Start Game' }}
                        </Button>
                    </div>
                </Card>
            </div>

            <!-- Loading Skeleton -->
            <div v-else class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <SkeletonCard v-for="i in 6" :key="i" />
            </div>

            <!-- Empty State -->
            <div
                v-if="!isLoading && getFilteredGames().length === 0"
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 sm:p-8 text-center dark:border-slate-700 dark:bg-slate-900/30"
            >
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400">
                    No games found matching your filters.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
