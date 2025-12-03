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

interface ProgressMetric {
    id: number;
    name: string;
    description: string;
    current: number;
    target: number;
    unit: string;
    icon: string;
    category: string;
}

interface LevelProgress {
    currentLevel: number;
    nextLevel: number;
    currentXP: number;
    xpForNextLevel: number;
    totalXPEarned: number;
}

interface Props {
    levelProgress: LevelProgress;
    metrics: ProgressMetric[];
    recentMilestones: {
        date: string;
        title: string;
        description: string;
    }[];
    stats: {
        totalAssignmentsCompleted: number;
        totalCoursesEnrolled: number;
        totalAssignmentsAvailable: number;
        averageCompletionRate: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Progress',
        href: '#',
    },
];

const selectedCategory = ref<string>('all');

const getCategories = () => {
    const categories = new Set(props.metrics.map(m => m.category));
    return Array.from(categories);
};

const getFilteredMetrics = () => {
    if (selectedCategory.value === 'all') {
        return props.metrics;
    }
    return props.metrics.filter(m => m.category === selectedCategory.value);
};

const getProgressPercentage = (metric: ProgressMetric) => {
    if (metric.target === 0) return 0;
    return Math.min((metric.current / metric.target) * 100, 100);
};

const getLevelProgressPercentage = () => {
    if (props.levelProgress.xpForNextLevel === 0) return 0;
    return (props.levelProgress.currentXP / props.levelProgress.xpForNextLevel) * 100;
};
</script>

<template>

    <Head title="Progress" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Level Progress Section -->
            <Card
                class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-r from-accent/20 to-accent/5">
                <CardHeader>
                    <CardTitle>Level Progress</CardTitle>
                    <CardDescription>Your journey to the next level</CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Current Level</p>
                            <p class="text-4xl font-bold">{{ levelProgress.currentLevel }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-muted-foreground mb-1">Next Level</p>
                            <p class="text-3xl font-bold text-accent">{{ levelProgress.nextLevel }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-muted-foreground mb-1">Total XP</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                {{ levelProgress.totalXPEarned.toLocaleString() }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Experience to Next Level</span>
                            <span class="text-sm text-muted-foreground">
                                {{ levelProgress.currentXP }} / {{ levelProgress.xpForNextLevel }}
                            </span>
                        </div>
                        <Progress :value="getLevelProgressPercentage()" class="h-3" />
                        <p class="text-xs text-muted-foreground text-right">
                            {{ Math.round(getLevelProgressPercentage()) }}% to Level {{ levelProgress.nextLevel }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Assignments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalAssignmentsCompleted }}</div>
                        <p class="text-xs text-muted-foreground mt-1">
                            of {{ stats.totalAssignmentsAvailable }} completed
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Courses</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalCoursesEnrolled }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Enrolled</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Completion</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.averageCompletionRate }}%</div>
                        <p class="text-xs text-muted-foreground mt-1">Average</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Progress Metrics -->
            <div>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold mb-3">Progress Metrics</h2>
                    <div class="flex gap-2 flex-wrap">
                        <button @click="selectedCategory = 'all'" :class="[
                            'px-3 py-1 rounded-full text-sm font-medium transition-colors',
                            selectedCategory === 'all'
                                ? 'bg-accent text-accent-foreground'
                                : 'bg-sidebar-border/20 text-muted-foreground hover:text-foreground'
                        ]">
                            All
                        </button>
                        <button v-for="category in getCategories()" :key="category" @click="selectedCategory = category"
                            :class="[
                                'px-3 py-1 rounded-full text-sm font-medium transition-colors capitalize',
                                selectedCategory === category
                                    ? 'bg-accent text-accent-foreground'
                                    : 'bg-sidebar-border/20 text-muted-foreground hover:text-foreground'
                            ]">
                            {{ category }}
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div v-for="metric in getFilteredMetrics()" :key="metric.id">
                        <Card class="border-sidebar-border/70 dark:border-sidebar-border h-full">
                            <CardContent class="pt-6">
                                <div class="flex items-start gap-4">
                                    <div class="text-3xl">{{ metric.icon }}</div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-sm mb-1">{{ metric.name }}</h3>
                                        <p class="text-xs text-muted-foreground mb-3">{{ metric.description }}</p>

                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium">
                                                   {{ metric.current }} {{ metric.unit }}
                                                </span>
                                                <span class="text-sm text-muted-foreground">
                                                    {{ Math.round(getProgressPercentage(metric)) }}%
                                                </span>
                                            </div>
                                            <Progress :value="getProgressPercentage(metric)" class="h-2" />
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Recent Milestones -->
            <!-- <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                <CardHeader>
                    <CardTitle>Recent Milestones</CardTitle>
                    <CardDescription>Your recent achievements and progress markers</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="(milestone, index) in recentMilestones" :key="index"
                            class="flex gap-4 pb-4 border-b border-sidebar-border/30 last:border-b-0 last:pb-0">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-accent/20 border border-accent/30">
                                    <span class="text-sm">✓</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm">{{ milestone.title }}</p>
                                <p class="text-sm text-muted-foreground">{{ milestone.description }}</p>
                                <p class="text-xs text-muted-foreground mt-1">{{ milestone.date }}</p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card> -->

            <!-- Continue Learning CTA -->
            <!-- <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-accent/30 to-accent/10">
                <CardContent class="pt-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold mb-1">Keep the momentum going!</h3>
                            <p class="text-sm text-muted-foreground">Complete your next lesson to earn more XP</p>
                        </div>
                        <Button size="lg">Continue Learning</Button>
                    </div>
                </CardContent>
            </Card> -->
        </div>
    </AppLayout>
</template>
