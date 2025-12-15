<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard, assignments as assignmentsRoute } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Progress from '@/components/ui/progress/Progress.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import StreakCard from '@/components/StreakCard.vue';
import StreakHeatmap from '@/components/StreakHeatmap.vue';
import DailyBonusModal from '@/components/DailyBonusModal.vue';
import ImprovedLeaderboard from '@/components/ImprovedLeaderboard.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Search } from 'lucide-vue-next';
import axios from 'axios';
import { watch, onMounted, onUnmounted, ref as vueRef } from 'vue';
import { useToast } from '@/composables/useToast';

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
    id: number;
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
    description?: string;
    content?: string;
    xp?: number;
    timestamp: string;
    id?: number;
}

interface Assignment {
    id: number;
    title: string;
    description: string;
    dueDate: string;
    isOverdue: boolean;
    submitted: boolean;
    status: string;
    grade: number | null;
}

interface StreakData {
    currentStreak: number;
    longestStreak: number;
    lastLoginDate: string | null;
    loginDates?: string[];
}

interface DailyBonusData {
    hasReceivedToday: boolean;
    xpAmount: number;
}

interface Props {
    userName: string;
    userStats: UserStats;
    courses: Course[];
    assignments: Assignment[];
    leaderboard: LeaderboardEntry[];
    achievements: Achievement[];
    recentActivity: Activity[];
    streak?: StreakData;
    dailyBonus?: DailyBonusData;
}

const props = defineProps<Props>();

const { achievement, quest, levelup, xp: addXP } = useToast();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const announcements = ref<Activity[]>([]);
const isLoadingAnnouncements = ref(false);
const isDailyBonusModalOpen = ref(false);
const refreshIntervals = vueRef<NodeJS.Timeout[]>([]);
const autoRefreshEnabled = ref(true);
const refreshInterval = 30000; // 30 seconds

const fetchAnnouncements = async () => {
    isLoadingAnnouncements.value = true;
    try {
        const response = await axios.get('/api/announcements/latest');
        announcements.value = response.data;
    } catch (error) {
        console.error('Failed to fetch announcements:', error);
    } finally {
        isLoadingAnnouncements.value = false;
    }
};

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/dashboard/stats');
        // Update user stats
        if (response.data.userStats) {
            Object.assign(props.userStats, response.data.userStats);
        }
        // Update courses
        if (response.data.courses) {
            Object.assign(props, { courses: response.data.courses });
        }
        // Update assignments
        if (response.data.assignments) {
            Object.assign(props, { assignments: response.data.assignments });
        }
        // Update leaderboard
        if (response.data.leaderboard) {
            Object.assign(props, { leaderboard: response.data.leaderboard });
        }
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
    }
};

const handleBonusClaimed = (result: any) => {
    // Update user stats with new XP
    if (result.total_xp !== undefined) {
        console.log('Bonus claimed! New total XP:', result.total_xp);
        // Refresh dashboard data after bonus claimed
        fetchDashboardData();
    }
};

const startAutoRefresh = () => {
    if (!autoRefreshEnabled.value) return;
    
    // Fetch announcements every 30 seconds
    const announcementsInterval = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchAnnouncements();
        }
    }, refreshInterval);
    
    // Fetch dashboard stats every 30 seconds
    const dashboardInterval = setInterval(() => {
        if (autoRefreshEnabled.value) {
            fetchDashboardData();
        }
    }, refreshInterval);
    
    refreshIntervals.value = [announcementsInterval, dashboardInterval];
};

const stopAutoRefresh = () => {
    refreshIntervals.value.forEach(interval => clearInterval(interval));
    refreshIntervals.value = [];
};

const handleVisibilityChange = () => {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
        // Fetch fresh data when tab becomes visible
        fetchAnnouncements();
        fetchDashboardData();
    }
};

// Fetch announcements on component mount and show daily bonus modal
onMounted(() => {
    fetchAnnouncements();
    fetchDashboardData();
    
    // Show daily bonus modal if user hasn't received it today
    if (props.dailyBonus && !props.dailyBonus.hasReceivedToday) {
        isDailyBonusModalOpen.value = true;
    }
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Listen for visibility changes (pause refresh when tab is hidden)
    document.addEventListener('visibilitychange', handleVisibilityChange);
});

onUnmounted(() => {
    stopAutoRefresh();
    document.removeEventListener('visibilitychange', handleVisibilityChange);
});

const progressPercentage = computed(() => {
    return (props.userStats.currentXP / props.userStats.maxXPForLevel) * 100;
});

const totalXPProgress = computed(() => {
    const maxTotalXP = 500000;
    return (props.userStats.totalXP / maxTotalXP) * 100;
});

const nextAchievementProgress = computed(() => {
    if (props.leaderboard.length === 0) return 0;
    const topXP = props.leaderboard[0]?.xp || 1;
    const userRank = props.leaderboard.find(l => l.isUser);
    return userRank ? (userRank.xp / topXP) * 100 : 0;
});

const topFiveLeaderboard = computed(() => {
    return props.leaderboard.slice(0, 5);
});

// Interactive state
const selectedCourse = ref<Course | null>(null);
const selectedAssignment = ref<Assignment | null>(null);
const selectedAchievement = ref<Achievement | null>(null);
const selectedLeaderboardEntry = ref<LeaderboardEntry | null>(null);
const isLevelCardHovered = ref(false);
const isTotalXPCardHovered = ref(false);
const isAchievementsCardHovered = ref(false);
const searchQuery = ref('');
const searchResults = ref<any[]>([]);
const isSearching = ref(false);

interface SearchUser {
    id: number;
    name: string;
    email: string;
    profile_photo_path?: string;
}

const handleCourseClick = (course: Course) => {
    selectedCourse.value = course;
};

const handleAssignmentClick = (assignment: Assignment) => {
    selectedAssignment.value = assignment;
};

const handleAchievementClick = (achievement: Achievement) => {
    selectedAchievement.value = achievement;
};

const handleLeaderboardClick = (leader: LeaderboardEntry) => {
    selectedLeaderboardEntry.value = leader;
};

const handleViewProfile = (userId: number) => {
    searchQuery.value = '';
    searchResults.value = [];
    router.visit(`/users/${userId}`);
};

const performSearch = async (query: string) => {
    if (query.trim().length === 0) {
        searchResults.value = [];
        return;
    }

    isSearching.value = true;
    try {
        const response = await axios.get('/api/users/search', {
            params: { query }
        });
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search error:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

watch(searchQuery, (newQuery) => {
    if (newQuery.trim().length > 0) {
        performSearch(newQuery);
    } else {
        searchResults.value = [];
    }
}, { debounce: 300 });

// Demo functions for toasts
const demoAchievementToast = () => {
    achievement('Achievement Unlocked!', 'You completed your first quiz', '🏆');
};

const demoQuestToast = () => {
    quest('Quest Completed!', 'You finished the Python Basics quest');
};

const demoLevelUpToast = () => {
    levelup(10, 250);
};

const demoXPToast = () => {
    addXP(50, 'Completing assignment');
};
</script>

<style scoped>
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.animate-shimmer {
    animation: shimmer 2s infinite;
}
</style>

<template>

    <Head title="Dashboard" />

    <!-- Daily Bonus Modal -->
    <DailyBonusModal 
        :open="isDailyBonusModalOpen"
        :has-received-today="props.dailyBonus?.hasReceivedToday ?? false"
        :xp-amount="props.dailyBonus?.xpAmount ?? 20"
        @update:open="isDailyBonusModalOpen = $event"
        @bonus-claimed="handleBonusClaimed"
    />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Hero Banner Section -->
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-accent/20 via-accent/10 to-transparent border border-accent/30 p-6 mb-4 shadow-sm">
                <!-- Decorative background elements -->
                <div class="absolute -right-20 -top-20 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-start justify-between gap-6">
                        <!-- User Info Section -->
                        <div class="flex flex-col gap-2 flex-1">
                            <h4 class="text-2xl font-bold text-foreground">Welcome, <span class="text-yellow-500 font-bold">{{ userName }}!</span></h4>
                            <p class="text-sm text-muted-foreground">Keep up your amazing progress and reach new milestones</p>
                            <div class="flex gap-2 mt-1">
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-accent/30 text-foreground font-medium">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                                    </svg>
                                    Active Learner
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-yellow-500/30 text-foreground font-medium">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    Committed
                                </span>
                            </div>
                        </div>
                        
                        <!-- Level Badge -->
                        <div class="flex flex-col items-center justify-center p-4 bg-background/50 backdrop-blur-sm rounded-lg border border-accent/30">
                            <div class="text-xs text-muted-foreground mb-1">Current Level</div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-yellow-500">{{ userStats.level }}</span>
                                <svg class="h-8 w-8 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- XP Progress Bar with Energy Effect - Total XP to 1M -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-xs text-muted-foreground">
                            <span class="font-medium">Total Experience Progress</span>
                            <span class="font-semibold text-foreground">{{ Math.round(totalXPProgress) }}%</span>
                        </div>
                        <div class="relative h-3 bg-background/50 rounded-full overflow-hidden border border-accent/20 shadow-inner">
                            <!-- Background shimmer effect -->
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse"></div>
                            
                            <!-- Main progress fill with dynamic animation -->
                            <div class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out relative"
                                :style="{ width: `${totalXPProgress}%` }">
                                <!-- Inner glow effect -->
                                <div class="absolute inset-0 bg-gradient-to-t from-transparent to-white/30 rounded-full"></div>
                                
                                <!-- Energy particles - animated fill effect -->
                                <div class="absolute inset-0 rounded-full overflow-hidden">
                                    <div class="absolute top-0 left-0 right-0 bottom-0 animate-pulse opacity-60"
                                        style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent); animation: shimmer 2s infinite;"></div>
                                </div>
                            </div>
                            
                            <!-- Outer glow for energy effect -->
                            <div v-if="totalXPProgress > 0" class="absolute top-0 left-0 h-full rounded-full blur-md opacity-40"
                                :style="{ width: `${totalXPProgress}%`, background: 'linear-gradient(90deg, rgba(250,204,21,0.6), rgba(234,179,8,0.4))' }"></div>
                        </div>
                        <div class="flex justify-between text-xs text-muted-foreground">
                            <span>{{ userStats.totalXP.toLocaleString() }} XP</span>
                            <span>500,000 XP</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Message & Search -->
            <div class="mb-2 space-y-4">
                <!-- Search Bar -->
                <div class="relative max-w-md">
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                        <input 
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search students..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-sidebar-border/70 bg-background hover:border-sidebar-border focus:outline-none focus:ring-2 focus:ring-accent/50 transition-all"
                        />
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div v-if="searchQuery && (searchResults.length > 0 || isSearching)" class="absolute top-full left-0 right-0 mt-2 bg-background border border-sidebar-border/70 rounded-lg shadow-lg z-50">
                        <!-- Loading State -->
                        <div v-if="isSearching" class="p-4 text-center text-muted-foreground text-sm">
                            Searching...
                        </div>

                        <!-- Results -->
                        <div v-else-if="searchResults.length > 0" class="max-h-96 overflow-y-auto">
                            <div 
                                v-for="user in searchResults" 
                                :key="user.id"
                                @click="handleViewProfile(user.id)"
                                class="flex items-center gap-3 p-3 border-b border-sidebar-border/30 last:border-b-0 hover:bg-accent/10 cursor-pointer transition-colors"
                            >
                                <!-- User Avatar -->
                                <div v-if="user.profile_photo_path" class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0">
                                    <img 
                                        :src="`/storage/${user.profile_photo_path}`"
                                        :alt="user.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div v-else class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center flex-shrink-0 text-sm font-bold">
                                    {{ user.name.split(' ').map((n: string) => n[0]).join('') }}
                                </div>

                                <!-- User Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ user.name }}</p>
                                    <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- No Results -->
                        <div v-else class="p-4 text-center text-muted-foreground text-sm">
                            No students found
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Section with User Stats -->
            <div class="grid gap-4 md:grid-cols-4">
                <!-- Level Card -->
                <Card
                    class="overflow-hidden border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isLevelCardHovered = true" @mouseleave="isLevelCardHovered = false">
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Level</CardTitle>
                        <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-baseline justify-between">
                            <div class="text-3xl font-bold">{{ userStats.level }}</div>
                            <span class="text-xs text-muted-foreground">{{ userStats.rank }}</span>
                        </div>
                        <div class="mt-4 space-y-2 overflow-hidden">
                            <!-- XP Bar Container (hidden by default, shown on hover) -->
                            <div class="transition-all duration-300 ease-out" :style="{
                                maxHeight: isLevelCardHovered ? '100px' : '0px',
                                opacity: isLevelCardHovered ? 1 : 0
                            }">
                                <div class="text-xs font-medium mb-1">Experience Progress</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated progress bar -->
                                    <div class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isLevelCardHovered ? `${progressPercentage}%` : '0%' }" />
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
                            <div class="transition-all duration-300" :style="{
                                maxHeight: isLevelCardHovered ? '0px' : '100px',
                                opacity: isLevelCardHovered ? 0 : 1
                            }">
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
                    @mouseenter="isTotalXPCardHovered = true" @mouseleave="isTotalXPCardHovered = false">
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Total XP</CardTitle>
                        <svg class="h-4 w-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ userStats.totalXP.toLocaleString() }}</div>
                        <div v-if="userStats.totalXP > 0" class="mt-4 space-y-2 overflow-hidden">
                            <!-- Detailed view on hover -->
                            <div class="transition-all duration-300 ease-out" :style="{
                                maxHeight: isTotalXPCardHovered ? '100px' : '0px',
                                opacity: isTotalXPCardHovered ? 1 : 0
                            }">
                                <div class="text-xs font-medium mb-1">Total Points</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated bar showing relative progress -->
                                    <div class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isTotalXPCardHovered ? '75%' : '0%' }" />
                                </div>
                                <div class="text-xs text-muted-foreground mt-2">
                                    Lifetime achievements unlocked
                                </div>
                            </div>

                            <!-- Compact display -->
                            <div class="transition-all duration-300" :style="{
                                maxHeight: isTotalXPCardHovered ? '0px' : '100px',
                                opacity: isTotalXPCardHovered ? 0 : 1
                            }">
                                <p class="text-xs text-muted-foreground">Points earned</p>
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p class="text-xs text-muted-foreground">No points earned yet</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Streak Card -->
                <StreakCard :streak="streak" />

                <!-- Achievements Card -->
                <Card
                    class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105"
                    @mouseenter="isAchievementsCardHovered = true" @mouseleave="isAchievementsCardHovered = false">
                    <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                        <CardTitle class="text-sm font-medium">Achievements</CardTitle>
                        <svg class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </CardHeader>
                    <CardContent>
                        <div class="text-3xl font-bold">{{ userStats.achievements }}</div>
                        <div v-if="userStats.achievements > 0" class="mt-4 space-y-2 overflow-hidden">
                            <!-- Detailed view on hover -->
                            <div class="transition-all duration-300 ease-out" :style="{
                                maxHeight: isAchievementsCardHovered ? '100px' : '0px',
                                opacity: isAchievementsCardHovered ? 1 : 0
                            }">
                                <div class="text-xs font-medium mb-1">Achievement Progress</div>
                                <div class="relative h-3 bg-muted rounded-full overflow-hidden">
                                    <!-- Animated bar for achievements -->
                                    <div class="h-full bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-600 rounded-full transition-all duration-1000 ease-out"
                                        :style="{ width: isAchievementsCardHovered ? '60%' : '0%' }" />
                                </div>
                                <div class="text-xs text-muted-foreground mt-2">
                                    Unlock more badges to progress
                                </div>
                            </div>

                            <!-- Compact display -->
                            <div class="transition-all duration-300" :style="{
                                maxHeight: isAchievementsCardHovered ? '0px' : '100px',
                                opacity: isAchievementsCardHovered ? 0 : 1
                            }">
                                <p class="text-xs text-muted-foreground">unlocked badges</p>
                            </div>
                        </div>
                        <div v-else class="mt-4">
                            <p class="text-xs text-muted-foreground">No badges unlocked yet</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Improved Leaderboard -->
            <ImprovedLeaderboard :leaderboard="leaderboard" />

            <!-- Streak Heatmap -->
            <Card v-if="streak?.loginDates" class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <span>📅</span>
                        <span>Activity Heatmap</span>
                    </CardTitle>
                    <CardDescription>Your login activity throughout the year</CardDescription>
                </CardHeader>
                <CardContent>
                    <StreakHeatmap :login-dates="streak.loginDates" />
                </CardContent>
            </Card>

            <!-- Main Content Grid -->
            <div class="grid gap-4 lg:grid-cols-3">
                <!-- Courses Progress - Main Section -->
                <div class="lg:col-span-2 space-y-4">
                    <!-- Active Courses & Assignments -->
                    <Card
                        class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle>Active Courses & Assignments</CardTitle>
                            <CardDescription>Your current learning journey</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="courses.length === 0 && assignments.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground">No active courses or assignments yet. Enroll in a
                                    course to get started!</p>
                            </div>

                            <!-- Courses Section -->
                            <div v-if="courses.length > 0" class="space-y-2">
                                <div v-for="course in courses" :key="course.id"
                                    class="space-y-2 p-2 rounded cursor-pointer transition-all duration-150 hover:bg-accent/10 hover:border border border-transparent"
                                    @click="handleCourseClick(course)">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-medium text-sm group-hover:text-accent">{{ course.name }}
                                            </h4>
                                            <p class="text-xs text-muted-foreground">
                                                {{ course.completedLessons }} / {{ course.totalLessons }} lessons
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-semibold text-yellow-500">+{{ course.xpEarned }}
                                            </div>
                                            <p class="text-xs text-muted-foreground">XP earned</p>
                                        </div>
                                    </div>
                                    <Progress :value="course.progress" class="h-2" />
                                    <div class="flex items-center justify-between text-xs text-muted-foreground">
                                        <span>{{ course.progress }}% complete</span>
                                        <span>Due: {{ course.nextDeadline }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Assignments Section -->
                            <div v-if="assignments.length > 0">
                                <div v-if="courses.length > 0" class="border-t my-4 pt-4">
                                    <h5 class="text-sm font-medium mb-2">Pending Assignments</h5>
                                </div>
                                <div v-for="assignment in assignments" :key="assignment.id"
                                    :class="['space-y-2 p-2 rounded cursor-pointer transition-all duration-150 border', assignment.isOverdue ? 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900' : assignment.submitted ? 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-900' : 'hover:bg-accent/10 border-transparent hover:border-accent/30']"
                                    @click="handleAssignmentClick(assignment)">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 flex-1">
                                            <div v-if="assignment.isOverdue"
                                                class="text-red-600 dark:text-red-400 font-bold">⚠</div>
                                            <div v-else-if="assignment.submitted"
                                                class="text-green-600 dark:text-green-400 font-bold">✓</div>
                                            <div v-else class="text-yellow-600 dark:text-yellow-400 font-bold">📋</div>
                                            <div>
                                                <h4 class="font-medium text-sm">{{ assignment.title }}</h4>
                                                <p class="text-xs text-muted-foreground">{{ assignment.description }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p
                                                :class="['text-sm font-medium', assignment.isOverdue ? 'text-red-600 dark:text-red-400' : assignment.submitted ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400']">
                                                {{ assignment.submitted ? 'Submitted' : 'Pending' }}
                                            </p>
                                            <p class="text-xs text-muted-foreground">Due: {{ assignment.dueDate }}</p>
                                        </div>
                                    </div>
                                    <div v-if="assignment.grade !== null" class="text-xs text-muted-foreground">
                                        Grade: <span class="font-medium">{{ assignment.grade }}/100</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Announcements -->
                    <Card
                        class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle>Announcements</CardTitle>
                            <CardDescription>Latest news and updates from admin</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="isLoadingAnnouncements" class="text-center py-8">
                                <p class="text-muted-foreground">Loading announcements...</p>
                            </div>
                            <div v-else-if="announcements.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground">No announcements yet</p>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="announcement in announcements" :key="announcement.id"
                                    class="flex items-start gap-3 pb-3 border-b last:border-b-0 p-2 rounded cursor-pointer transition-all duration-150 hover:bg-accent/10">
                                    <div class="mt-1 text-lg">
                                        <span>📢</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ announcement.title }}</p>
                                        <p v-if="announcement.description" class="text-xs text-muted-foreground line-clamp-2">{{ announcement.description }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">{{ announcement.timestamp }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar - Achievements -->
                <div class="space-y-4">
                    <!-- Unlocked Achievements -->
                    <Card
                        class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg">
                        <CardHeader>
                            <CardTitle class="text-sm">Achievements</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-3 gap-2">
                                <div v-for="achievement in achievements.filter(a => a.unlocked)" :key="achievement.id"
                                    :class="['flex flex-col items-center justify-center p-2 rounded border cursor-pointer transition-all duration-150 hover:scale-110', achievement.unlocked ? 'bg-accent/20 border-sidebar-border/50 hover:border-accent hover:shadow-sm' : 'opacity-50 grayscale border-sidebar-border/50 hover:opacity-75']"
                                    :title="achievement.description" @click="handleAchievementClick(achievement)">
                                    <div class="text-2xl mb-1">{{ achievement.icon }}</div>
                                    <p class="text-xs text-center text-muted-foreground">{{ achievement.name }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Next Goal
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
                    </Card> -->
                </div>
            </div>
        </div>

        <!-- Course Details Dialog -->
        <Dialog v-if="selectedCourse" :open="!!selectedCourse"
            @update:open="(open) => !open && (selectedCourse = null)">
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
                        <p class="text-sm text-muted-foreground">{{ selectedCourse.completedLessons }} / {{
                            selectedCourse.totalLessons }} completed</p>
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

        <!-- Assignment Details Dialog -->
        <Dialog v-if="selectedAssignment" :open="!!selectedAssignment"
            @update:open="(open) => !open && (selectedAssignment = null)">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <span v-if="selectedAssignment.isOverdue" class="text-red-600">⚠</span>
                        <span v-else-if="selectedAssignment.submitted" class="text-green-600">✓</span>
                        <span v-else class="text-yellow-600">📋</span>
                        {{ selectedAssignment.title }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ selectedAssignment.description }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div
                        :class="['p-3 rounded-lg border', selectedAssignment.isOverdue ? 'bg-red-50 dark:bg-red-950/20 border-red-200 dark:border-red-900' : selectedAssignment.submitted ? 'bg-green-50 dark:bg-green-950/20 border-green-200 dark:border-green-900' : 'bg-yellow-50 dark:bg-yellow-950/20 border-yellow-200 dark:border-yellow-900']">
                        <p
                            :class="['text-sm font-medium', selectedAssignment.isOverdue ? 'text-red-700 dark:text-red-300' : selectedAssignment.submitted ? 'text-green-700 dark:text-green-300' : 'text-yellow-700 dark:text-yellow-300']">
                            {{ selectedAssignment.isOverdue ? '⚠ Overdue' : selectedAssignment.submitted ? '✓ Submitted'
                                : '📋 Pending Submission' }}
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Due Date</h4>
                        <p class="text-sm text-muted-foreground">{{ selectedAssignment.dueDate }}</p>
                    </div>
                    <div v-if="selectedAssignment.grade !== null">
                        <h4 class="font-medium mb-2">Grade</h4>
                        <p class="text-sm font-semibold text-accent">{{ selectedAssignment.grade }}/100</p>
                    </div>
                    <div v-if="selectedAssignment.submitted">
                        <h4 class="font-medium mb-2">Status</h4>
                        <p class="text-sm text-green-600 dark:text-green-400 capitalize">{{ selectedAssignment.status }}
                        </p>
                    </div>
                    <Button class="w-full" :variant="selectedAssignment.submitted ? 'outline' : 'default'"
                        @click="$inertia.visit(assignmentsRoute().url)">
                        {{ selectedAssignment.submitted ? 'View Submission' : 'Submit Assignment' }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Achievement Details Dialog -->
        <Dialog v-if="selectedAchievement" :open="!!selectedAchievement"
            @update:open="(open) => !open && (selectedAchievement = null)">
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
                        <p v-if="selectedAchievement.unlocked" class="text-sm font-medium text-accent">✓ Achievement
                            Unlocked</p>
                        <p v-else class="text-sm font-medium text-muted-foreground">Locked - Keep working to unlock this
                            achievement!</p>
                    </div>
                    <Button class="w-full" :variant="selectedAchievement.unlocked ? 'default' : 'outline'">
                        {{ selectedAchievement.unlocked ? 'View Badge' : 'Continue Learning' }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Leaderboard Details Dialog -->
        <Dialog v-if="selectedLeaderboardEntry" :open="!!selectedLeaderboardEntry"
            @update:open="(open) => !open && (selectedLeaderboardEntry = null)">
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
                    <Button class="w-full" variant="outline" @click="handleViewProfile(selectedLeaderboardEntry.id)">View Profile</Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
