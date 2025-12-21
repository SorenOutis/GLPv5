<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Code2, Zap, Target, BookOpen, Github } from 'lucide-vue-next';

interface Challenge {
    id: number;
    title: string;
    difficulty: string;
    description: string;
    xpReward: number;
    completed: boolean;
    language: string;
    category: string;
}

interface Resource {
    id: number;
    title: string;
    description: string;
    type: string;
    difficulty: string;
    duration: string;
}

interface Stats {
    totalChallenges: number;
    completedChallenges: number;
    xpEarned: number;
    currentStreak: number;
    totalXP: number;
    level: number;
}

interface Props {
    challenges: Challenge[];
    stats: Stats;
    resources: Resource[];
    userLevel: number;
    userName: string;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Coding',
        href: '#',
    },
];

const selectedFilter = ref<'all' | 'beginner' | 'intermediate' | 'advanced'>('all');
const selectedLanguage = ref<'all' | 'JavaScript' | 'Python' | 'Java'>('all');

const filteredChallenges = computed(() => {
    return props.challenges.filter(challenge => {
        const difficultyMatch = selectedFilter.value === 'all' || challenge.difficulty.toLowerCase() === selectedFilter.value;
        const languageMatch = selectedLanguage.value === 'all' || challenge.language === selectedLanguage.value;
        return difficultyMatch && languageMatch;
    });
});

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Beginner': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Intermediate': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Advanced': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
};

const getDifficultyIcon = (difficulty: string) => {
    switch (difficulty) {
        case 'Beginner':
            return '⚡';
        case 'Intermediate':
            return '🔥';
        case 'Advanced':
            return '💪';
        default:
            return '⭐';
    }
};

const getResourceTypeColor = (type: string) => {
    const colors: Record<string, string> = {
        'Tutorial': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'Course': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'Challenge': 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
    };
    return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
};

const openChallenge = (challengeId: number) => {
    router.visit(`/coding/${challengeId}`);
};
</script>

<template>
    <Head title="Coding Challenges" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header Section -->
            <div class="space-y-2 mb-6">
                <div class="flex items-center gap-2">
                    <Code2 class="w-8 h-8 text-primary" />
                    <h1 class="text-3xl font-bold">Coding Challenges</h1>
                </div>
                <p class="text-muted-foreground">Master your programming skills with interactive coding challenges</p>
            </div>

            <!-- Stats Section -->
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-blue-50 to-blue-50/50 dark:from-blue-950/20 dark:to-blue-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium flex items-center gap-2">
                            <Target class="w-4 h-4" />
                            Total Challenges
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalChallenges }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Available to solve</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-green-50 to-green-50/50 dark:from-green-950/20 dark:to-green-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium flex items-center gap-2">
                            <Zap class="w-4 h-4" />
                            Completed
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.completedChallenges }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Challenges solved</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-purple-50 to-purple-50/50 dark:from-purple-950/20 dark:to-purple-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium flex items-center gap-2">
                            <Zap class="w-4 h-4" />
                            XP Earned
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.xpEarned }}</div>
                        <p class="text-xs text-muted-foreground mt-1">From coding</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-orange-50 to-orange-50/50 dark:from-orange-950/20 dark:to-orange-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium flex items-center gap-2">
                            <Code2 class="w-4 h-4" />
                            Current Streak
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.currentStreak }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Days in a row</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Filters Section -->
            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="flex gap-2 flex-wrap">
                    <Button
                        v-for="difficulty in ['all', 'beginner', 'intermediate', 'advanced']"
                        :key="difficulty"
                        @click="selectedFilter = difficulty as any"
                        :variant="selectedFilter === difficulty ? 'default' : 'outline'"
                        class="capitalize"
                    >
                        {{ difficulty }}
                    </Button>
                </div>
                <div class="flex gap-2 flex-wrap md:ml-auto">
                    <Button
                        v-for="lang in ['all', 'JavaScript', 'Python', 'Java']"
                        :key="lang"
                        @click="selectedLanguage = lang as any"
                        :variant="selectedLanguage === lang ? 'default' : 'outline'"
                    >
                        {{ lang }}
                    </Button>
                </div>
            </div>

            <!-- Challenges Grid -->
            <div class="space-y-4 mb-6">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <Code2 class="w-5 h-5" />
                    Challenges
                </h2>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="challenge in filteredChallenges"
                        :key="challenge.id"
                        class="group relative"
                        @click="openChallenge(challenge.id)"
                    >
                        <Card class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer h-full">
                            <div class="absolute top-0 left-0 w-1 h-full transition-all duration-300"
                                :class="{
                                    'bg-green-500': challenge.difficulty === 'Beginner',
                                    'bg-yellow-500': challenge.difficulty === 'Intermediate',
                                    'bg-red-500': challenge.difficulty === 'Advanced',
                                }">
                            </div>

                            <CardHeader>
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <CardTitle class="text-lg">{{ challenge.title }}</CardTitle>
                                        <CardDescription>{{ challenge.category }}</CardDescription>
                                    </div>
                                    <span class="text-2xl">{{ getDifficultyIcon(challenge.difficulty) }}</span>
                                </div>
                            </CardHeader>

                            <CardContent class="space-y-4">
                                <p class="text-sm text-muted-foreground">{{ challenge.description }}</p>
                                
                                <div class="flex flex-wrap gap-2">
                                    <span :class="['px-2 py-1 rounded text-xs font-medium', getDifficultyColor(challenge.difficulty)]">
                                        {{ challenge.difficulty }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                                        {{ challenge.language }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-sidebar-border/30">
                                    <div class="flex items-center gap-1">
                                        <Zap class="w-4 h-4 text-yellow-500" />
                                        <span class="text-sm font-semibold">{{ challenge.xpReward }} XP</span>
                                    </div>
                                    <Button size="sm" :variant="challenge.completed ? 'secondary' : 'default'">
                                        {{ challenge.completed ? '✓ Completed' : 'Solve' }}
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <div v-if="filteredChallenges.length === 0" class="text-center py-12">
                    <Code2 class="w-12 h-12 text-muted-foreground mx-auto mb-4 opacity-50" />
                    <p class="text-muted-foreground">No challenges found for the selected filters</p>
                </div>
            </div>

            <!-- Resources Section -->
            <div class="space-y-4">
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <BookOpen class="w-5 h-5" />
                    Featured Resources
                </h2>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card
                        v-for="resource in resources"
                        :key="resource.id"
                        class="border-sidebar-border/70 dark:border-sidebar-border hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                    >
                        <CardHeader>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <CardTitle class="text-lg">{{ resource.title }}</CardTitle>
                                </div>
                                <BookOpen class="w-5 h-5 text-muted-foreground" />
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <p class="text-sm text-muted-foreground">{{ resource.description }}</p>
                            
                            <div class="flex flex-wrap gap-2">
                                <span :class="['px-2 py-1 rounded text-xs font-medium', getResourceTypeColor(resource.type)]">
                                    {{ resource.type }}
                                </span>
                                <span :class="['px-2 py-1 rounded text-xs font-medium', getDifficultyColor(resource.difficulty)]">
                                    {{ resource.difficulty }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-sidebar-border/30">
                                <span class="text-xs text-muted-foreground">{{ resource.duration }}</span>
                                <Button size="sm" variant="outline">
                                    Learn →
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
