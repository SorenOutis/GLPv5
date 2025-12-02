<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Progress from '@/components/ui/progress/Progress.vue';
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

interface UserStats {
    totalXP: number;
    level: number;
    currentXP: number;
    maxXPForLevel: number;
    rank: string;
    streakDays: number;
    achievements: number;
}

interface Course {
    id: number;
    name: string;
    progress: number;
    completedLessons: number;
    totalLessons: number;
    xpEarned: number;
    nextDeadline: string;
}

interface LeaderboardEntry {
    rank: number;
    name: string;
    xp: number;
    level: number;
    badge: string;
    isUser?: boolean;
}

interface Achievement {
    id: number;
    name: string;
    description: string;
    unlocked: boolean;
    icon: string;
}

interface Activity {
    type: string;
    title: string;
    xp: number;
    timestamp: string;
}

interface Props {
    userStats: UserStats;
    courses: Course[];
    leaderboard: LeaderboardEntry[];
    achievements: Achievement[];
    recentActivity: Activity[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const progressPercentage = computed(() => {
    return (props.userStats.currentXP / props.userStats.maxXPForLevel) * 100;
});

const nextAchievementProgress = computed(() => {
    if (props.leaderboard.length === 0) return 0;
    const topXP = props.leaderboard[0]?.xp || 1;
    const userRank = props.leaderboard.find(l => l.isUser);
    return userRank ? (userRank.xp / topXP) * 100 : 0;
});

// Interactive state
const selectedCourse = ref<Course | null>(null);
const selectedAchievement = ref<Achievement | null>(null);
const selectedLeaderboardEntry = ref<LeaderboardEntry | null>(null);
const isLevelCardHovered = ref(false);
const isTotalXPCardHovered = ref(false);
const isStreakCardHovered = ref(false);
const isAchievementsCardHovered = ref(false);

const handleCourseClick = (course: Course) => {
    selectedCourse.value = course;
};

const handleAchievementClick = (achievement: Achievement) => {
    selectedAchievement.value = achievement;
};

const handleLeaderboardClick = (leader: LeaderboardEntry) => {
    selectedLeaderboardEntry.value = leader;
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Header Section with User Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <!-- Level Card -->
                <Card 
                    class="overflow-hidden border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isLevelCardHovered = true"
                    @mouseleave="isLevelCardHovered = false"
                >
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Level</CardTitle>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-baseline justify-between">
                            <div class="text-3xl font-bold">{{ userStats.level }}</div>
                            <span class="text-xs text-muted-foreground">{{ userStats.rank }}</span>
                        </div>
                        <div class="mt-4 space-y-2 overflow-hidden">
                            <!-- XP Bar Container (hidden by default, shown on hover) -->
                            <div 
                                class="transition-all duration-300 ease-out"
                                :style="{ 
                                    maxHeight: isLevelCardHovered ? '100px' : '0px',
                                    opacity: isLevelCardHovered ? 1 : 0
                                }"
                            >
                                <div class="text-xs font-medium mb-1">Experience Progress</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated progress bar -->
                                    <div 
                                        class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isLevelCardHovered ? `${progressPercentage}%` : '0%' }"
                                    />
                                </div>
                                <div class="flex justify-between text-xs text-muted-foreground mt-1">
                                    <span>{{ userStats.currentXP }}</span>
                                    <span>{{ userStats.maxXPForLevel }}</span>
                                </div>
                                <div class="text-xs font-medium text-center mt-2">
                                    {{ Math.round(progressPercentage) }}% to next level
                                </div>
                            </div>
                            
                            <!-- Compact XP display (shown when not hovering) -->
                            <div 
                                class="transition-all duration-300"
                                :style="{ 
                                    maxHeight: isLevelCardHovered ? '0px' : '100px',
                                    opacity: isLevelCardHovered ? 0 : 1
                                }"
                            >
                                <div class="text-xs text-muted-foreground">
                                    {{ userStats.currentXP }} / {{ userStats.maxXPForLevel }} XP
                                </div>
                                <Progress :value="progressPercentage" class="mt-2 h-2" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Total XP Card -->
                <Card 
                    class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isTotalXPCardHovered = true"
                    @mouseleave="isTotalXPCardHovered = false"
                >
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Total XP</CardTitle>
                        <svg class="h-4 w-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ userStats.totalXP.toLocaleString() }}</div>
                        <div v-if="userStats.totalXP > 0" class="mt-4 space-y-2 overflow-hidden">
                            <!-- Detailed view on hover -->
                            <div 
                                class="transition-all duration-300 ease-out"
                                :style="{ 
                                    maxHeight: isTotalXPCardHovered ? '100px' : '0px',
                                    opacity: isTotalXPCardHovered ? 1 : 0
                                }"
                            >
                                <div class="text-xs font-medium mb-1">Total Points</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated bar showing relative progress -->
                                    <div 
                                        class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isTotalXPCardHovered ? '75%' : '0%' }"
                                    />
                                </div>
                                <div class="text-xs text-muted-foreground mt-2">
                                    Lifetime achievements unlocked
                                </div>
                            </div>
                            
                            <!-- Compact display -->
                            <div 
                                class="transition-all duration-300"
                                :style="{ 
                                    maxHeight: isTotalXPCardHovered ? '0px' : '100px',
                                    opacity: isTotalXPCardHovered ? 0 : 1
                                }"
                            >
                                <p class="text-xs text-muted-foreground">Points earned</p>
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p class="text-xs text-muted-foreground">No points earned yet</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Streak Card -->
                <Card 
                    class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isStreakCardHovered = true"
                    @mouseleave="isStreakCardHovered = false"
                >
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Streak</CardTitle>
                        <svg class="h-4 w-4 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.49 5.48c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm-3.6 13.9h8.22v-1.5h-8.22v1.5zm0-3h8.22v-1.5h-8.22v1.5zm0-3h8.22v-1.5h-8.22v1.5z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ userStats.streakDays }}</div>
                        <div class="mt-4 space-y-2 overflow-hidden">
                            <!-- Detailed view on hover -->
                            <div 
                                class="transition-all duration-300 ease-out"
                                :style="{ 
                                    maxHeight: isStreakCardHovered ? '100px' : '0px',
                                    opacity: isStreakCardHovered ? 1 : 0
                                }"
                            >
                                <div class="text-xs font-medium mb-1">Consistency Progress</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated bar for streak -->
                                    <div 
                                        class="h-full bg-gradient-to-r from-orange-400 via-orange-500 to-red-500 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isStreakCardHovered ? `${Math.min(userStats.streakDays * 10, 100)}%` : '0%' }"
                                    />
                                </div>
                                <div class="text-xs text-muted-foreground mt-2">
                                    Keep the momentum going!
                                </div>
                            </div>
                            
                            <!-- Compact display -->
                            <div 
                                class="transition-all duration-300"
                                :style="{ 
                                    maxHeight: isStreakCardHovered ? '0px' : '100px',
                                    opacity: isStreakCardHovered ? 0 : 1
                                }"
                            >
                                <p class="text-xs text-muted-foreground">days in a row</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Achievements Card -->
                <Card 
                    class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isAchievementsCardHovered = true"
                    @mouseleave="isAchievementsCardHovered = false"
                >
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Achievements</CardTitle>
                        <svg class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ userStats.achievements }}</div>
                        <div v-if="userStats.achievements > 0" class="mt-4 space-y-2 overflow-hidden">
                            <!-- Detailed view on hover -->
                            <div 
                                class="transition-all duration-300 ease-out"
                                :style="{ 
                                    maxHeight: isAchievementsCardHovered ? '100px' : '0px',
                                    opacity: isAchievementsCardHovered ? 1 : 0
                                }"
                            >
                                <div class="text-xs font-medium mb-1">Achievement Progress</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated bar for achievements -->
                                    <div 
                                        class="h-full bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isAchievementsCardHovered ? '60%' : '0%' }"
                                    />
                                </div>
                                <div class="text-xs text-muted-foreground mt-2">
                                    Unlock more badges to progress
                                </div>
                            </div>
                            
                            <!-- Compact display -->
                            <div 
                                class="transition-all duration-300"
                                :style="{ 
                                    maxHeight: isAchievementsCardHovered ? '0px' : '100px',
                                    opacity: isAchievementsCardHovered ? 0 : 1
                                }"
                            >
                                <p class="text-xs text-muted-foreground">unlocked badges</p>
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p class="text-xs text-muted-foreground">No badges unlocked yet</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Courses Progress - Main Section -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Active Courses -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle>Active Courses</CardTitle>
                            <CardDescription>Your current learning journey</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="courses.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground">No active courses yet. Enroll in a course to get started!</p>
                            </div>
                            <div v-for="course in courses" :key="course.id" class="space-y-2 p-2 rounded cursor-pointer transition-all duration-150 hover:bg-accent/10 hover:border border border-transparent" @click="handleCourseClick(course)">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-sm group-hover:text-accent">{{ course.name }}</h4>
                                        <p class="text-xs text-muted-foreground">
                                            {{ course.completedLessons }} / {{ course.totalLessons }} lessons
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-yellow-500">+{{ course.xpEarned }}</div>
                                        <p class="text-xs text-muted-foreground">XP earned</p>
                                    </div>
                                </div>
                                <Progress :value="course.progress" class="h-2" />
                                <div class="flex items-center justify-between text-xs text-muted-foreground">
                                    <span>{{ course.progress }}% complete</span>
                                    <span>Due: {{ course.nextDeadline }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Activity -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle>Recent Activity</CardTitle>
                            <CardDescription>Your latest achievements and progress</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="recentActivity.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground">No recent activity yet. Start learning to see your progress here!</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="(activity, index) in recentActivity" :key="index" class="flex items-start gap-3 pb-3 border-b last:border-b-0 p-2 rounded cursor-pointer transition-all duration-150 hover:bg-accent/10">
                                    <div class="mt-1 text-lg">
                                        <span v-if="activity.type === 'lesson'">📚</span>
                                        <span v-else-if="activity.type === 'quiz'">✅</span>
                                        <span v-else-if="activity.type === 'achievement'">⭐</span>
                                        <span v-else>🚀</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ activity.title }}</p>
                                        <p class="text-xs text-muted-foreground">{{ activity.timestamp }}</p>
                                    </div>
                                    <div v-if="activity.xp > 0" class="text-right">
                                        <p class="text-sm font-semibold text-yellow-500">+{{ activity.xp }} XP</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar - Leaderboard & Achievements -->
                <div class="space-y-4">
                    <!-- Leaderboard -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle>Leaderboard</CardTitle>
                            <CardDescription>Top learners this month</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="leaderboard.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground text-sm">No leaderboard data yet</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="leader in leaderboard" :key="leader.rank"
                                    :class="['flex items-center gap-3 p-2 rounded cursor-pointer transition-all duration-150', leader.isUser ? 'bg-accent/30 border border-accent' : 'hover:bg-accent/10 border border-transparent hover:border-accent/30']"
                                    @click="handleLeaderboardClick(leader)">
                                    <div class="font-bold text-lg w-6">{{ leader.badge }}</div>
                                    <div class="flex-1">
                                        <p :class="['text-sm font-medium', leader.isUser && 'font-bold']">{{ leader.name }}</p>
                                        <p class="text-xs text-muted-foreground">Lvl {{ leader.level }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold">{{ leader.xp.toLocaleString() }}</p>
                                        <p class="text-xs text-muted-foreground">XP</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Unlocked Achievements -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle class="text-sm">Achievements</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-3 gap-2">
                                <div v-for="achievement in achievements" :key="achievement.id"
                                    :class="['flex flex-col items-center justify-center p-2 rounded border cursor-pointer transition-all duration-150 hover:scale-110', achievement.unlocked ? 'bg-accent/20 border-sidebar-border/50 hover:border-accent hover:shadow-sm' : 'opacity-50 grayscale border-sidebar-border/50 hover:opacity-75']"
                                    :title="achievement.description"
                                    @click="handleAchievementClick(achievement)">
                                    <div class="text-2xl mb-1">{{ achievement.icon }}</div>
                                    <p class="text-xs text-center text-muted-foreground">{{ achievement.name }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Next Goal -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-accent/30 to-accent/10 transition-all duration-200 hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105">
                        <CardHeader>
                            <CardTitle class="text-sm">Next Goal</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <p class="text-sm font-medium">Reach Level {{ userStats.level + 1 }}</p>
                                <Progress :value="progressPercentage" class="h-2" />
                                <p class="text-xs text-muted-foreground">{{ Math.round(progressPercentage) }}% of way to next level</p>
                                <Button class="w-full mt-3" size="sm">Continue Learning</Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Course Details Dialog -->
        <Dialog v-if="selectedCourse" :open="!!selectedCourse" @update:open="(open) => !open && (selectedCourse = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{{ selectedCourse.name }}</DialogTitle>
                    <DialogDescription>
                        Course Details
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium mb-2">Progress</h4>
                        <Progress :value="selectedCourse.progress" class="h-2 mb-2" />
                        <p class="text-sm text-muted-foreground">{{ selectedCourse.progress }}% complete</p>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Lessons</h4>
                        <p class="text-sm text-muted-foreground">{{ selectedCourse.completedLessons }} / {{ selectedCourse.totalLessons }} completed</p>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">XP Earned</h4>
                        <p class="text-sm text-yellow-500 font-semibold">+{{ selectedCourse.xpEarned }} XP</p>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Next Deadline</h4>
                        <p class="text-sm text-muted-foreground">{{ selectedCourse.nextDeadline }}</p>
                    </div>
                    <Button class="w-full">Continue Course</Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Achievement Details Dialog -->
        <Dialog v-if="selectedAchievement" :open="!!selectedAchievement" @update:open="(open) => !open && (selectedAchievement = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <span class="text-3xl">{{ selectedAchievement.icon }}</span>
                        {{ selectedAchievement.name }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ selectedAchievement.description }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-accent/10 border border-accent/30">
                        <p v-if="selectedAchievement.unlocked" class="text-sm font-medium text-accent">✓ Achievement Unlocked</p>
                        <p v-else class="text-sm font-medium text-muted-foreground">Locked - Keep working to unlock this achievement!</p>
                    </div>
                    <Button class="w-full" :variant="selectedAchievement.unlocked ? 'default' : 'outline'">
                        {{ selectedAchievement.unlocked ? 'View Badge' : 'Continue Learning' }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Leaderboard Details Dialog -->
        <Dialog v-if="selectedLeaderboardEntry" :open="!!selectedLeaderboardEntry" @update:open="(open) => !open && (selectedLeaderboardEntry = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <span class="text-2xl">{{ selectedLeaderboardEntry.badge }}</span>
                        {{ selectedLeaderboardEntry.name }}
                    </DialogTitle>
                    <DialogDescription>
                        Rank #{{ selectedLeaderboardEntry.rank }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-muted-foreground mb-1">Level</p>
                            <p class="text-2xl font-bold">{{ selectedLeaderboardEntry.level }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground mb-1">Total XP</p>
                            <p class="text-2xl font-bold">{{ selectedLeaderboardEntry.xp.toLocaleString() }}</p>
                        </div>
                    </div>
                    <Button class="w-full" variant="outline">View Profile</Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
