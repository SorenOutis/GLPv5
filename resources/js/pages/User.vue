<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, withDefaults } from 'vue';
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

interface UserProfile {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    cover_photo_path?: string;
    profile_photo_path?: string;
    bio?: string;
    joinedDate: string;
}

interface UserStats {
    totalXP: number;
    level: number;
    coursesEnrolled: number;
    assignmentsCompleted: number;
    achievementsUnlocked: number;
    currentStreak: number;
    rank: number;
    rankTitle: string;
}

interface UserProgress {
    courseId: number;
    courseName: string;
    progress: number;
    completedLessons: number;
    totalLessons: number;
}

interface UserAchievement {
    id: number;
    name: string;
    description: string;
    icon: string;
    unlockedAt: string;
}

interface Props {
    user: UserProfile;
    stats: UserStats;
    progress: UserProgress[];
    achievements: UserAchievement[];
    isOwnProfile?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isOwnProfile: true,
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'User Profile',
        href: '#',
    },
];

const selectedAchievement = ref<UserAchievement | null>(null);
const profileHovered = ref(false);
const particles = ref<Array<{ id: number; x: number; y: number }>>([]);
const particleCounter = ref(0);
const levelProgressAnimated = ref(0);

// Cover photo state
const coverPhotoPreview = ref<string | null>(null);
const coverPhotoInput = ref<HTMLInputElement | null>(null);

// Animated stats
const animatedStats = ref([
    { label: 'XP', value: 0, target: props.stats.totalXP, icon: '⭐', suffix: '' },
    { label: 'Rank', value: 0, target: props.stats.rank, icon: '🏅', suffix: '', isRank: true },
    { label: 'Level', value: 0, target: props.stats.level, icon: '👑', suffix: '' },
    { label: 'Courses', value: 0, target: props.stats.coursesEnrolled, icon: '📚', suffix: '' },
    { label: 'Assignments', value: 0, target: props.stats.assignmentsCompleted, icon: '✓', suffix: '' },
    { label: 'Streak', value: 0, target: props.stats.currentStreak, icon: '🔥', suffix: ' days' },
]);

const nextLevelXp = computed(() => (props.stats.level + 1) * 1000);
const currentLevelXp = computed(() => props.stats.level * 1000);
const xpProgress = computed(() => {
    const current = (props.stats.totalXP % 1000);
    return (current / 1000) * 100;
});

onMounted(() => {
    // Animate stat counters
    animatedStats.value.forEach((stat) => {
        let current = 0;
        const increment = Math.ceil(stat.target / 40);
        const interval = setInterval(() => {
            current += increment;
            if (current >= stat.target) {
                stat.value = stat.target;
                clearInterval(interval);
            } else {
                stat.value = current;
            }
        }, 30);
    });

    // Animate XP progress bar
    const xpInterval = setInterval(() => {
        if (levelProgressAnimated.value < xpProgress.value) {
            levelProgressAnimated.value = Math.min(
                levelProgressAnimated.value + 2,
                xpProgress.value
            );
        }
    }, 50);

    return () => clearInterval(xpInterval);
});

const createParticle = (event: MouseEvent) => {
    const rect = (event.currentTarget as HTMLElement).getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    const particle = {
        id: particleCounter.value++,
        x,
        y,
    };

    particles.value.push(particle);

    setTimeout(() => {
        particles.value = particles.value.filter((p) => p.id !== particle.id);
    }, 600);
};

const handleEditClick = () => {
    router.visit('/settings/profile');
};

const handleAchievementClick = (achievement: UserAchievement) => {
    selectedAchievement.value = achievement;
};

const getStreakEmoji = () => {
    const streak = props.stats.currentStreak;
    if (streak === 0) return '❄️';
    if (streak < 7) return '🔥';
    if (streak < 30) return '🔥🔥';
    return '🔥🔥🔥';
};

const openCoverPhotoUpload = () => {
    router.visit('/settings/profile');
};

const getCoverPhotoUrl = () => {
    if (coverPhotoPreview.value) {
        return coverPhotoPreview.value;
    }
    if (props.user.cover_photo_path) {
        return `/storage/${props.user.cover_photo_path}`;
    }
    return null;
};
</script>

<template>
    <Head title="User Profile" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- User Header Section - Game Style -->
            <div
                class="relative overflow-hidden rounded-xl border-2 border-sidebar-border/70 dark:border-sidebar-border shadow-lg transition-all duration-300 hover:shadow-xl hover:border-sidebar-border"
                :style="getCoverPhotoUrl() ? {
                    backgroundImage: `url('${getCoverPhotoUrl()}')`,
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                } : {}"
                @mouseenter="profileHovered = true"
                @mouseleave="profileHovered = false"
            >
                <!-- Cover Photo Overlay -->
                <div v-if="!getCoverPhotoUrl()" class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 z-0" />
                <div v-if="getCoverPhotoUrl()" class="absolute inset-0 bg-black/40 z-0" />

                <!-- Change Cover Button
                <button v-if="getCoverPhotoUrl()" @click="openCoverPhotoUpload"
                    class="absolute top-3 right-3 bg-black/50 hover:bg-black/70 text-white px-3 py-2 rounded-lg text-sm transition-colors z-20">
                    📸 Change Cover
                </button> -->

                <!-- Add Cover Photo Placeholder -->
                <div v-if="!getCoverPhotoUrl() && isOwnProfile" @click="openCoverPhotoUpload"
                    class="absolute inset-0 w-full h-full flex items-center justify-center group cursor-pointer z-10">
                    <div class="text-center transition-transform group-hover:scale-110">
                        <div class="text-4xl mb-2">📸</div>
                        <div class="text-purple-300 group-hover:text-purple-200 font-medium">Add Cover Photo</div>
                        <div class="text-xs text-purple-300/60 mt-1">Click to upload</div>
                    </div>
                </div>

                <!-- Animated background -->
                <div class="absolute inset-0 opacity-10 z-5">
                    <div
                        class="absolute -right-1/2 -top-1/2 h-full w-full rounded-full bg-gradient-to-br from-accent to-transparent blur-3xl transition-transform duration-500"
                        :class="{ 'scale-110': profileHovered }"
                    />
                </div>

                <!-- Particle effects -->
                <div v-for="particle in particles" :key="particle.id" class="absolute pointer-events-none z-15">
                    <div
                        class="text-2xl"
                        :style="{
                            left: particle.x + 'px',
                            top: particle.y + 'px',
                            animation: `float-up 0.6s ease-out forwards`,
                        }"
                    >
                        ✨
                    </div>
                </div>

                <div class="relative z-20 flex items-start justify-between p-8 min-h-80">
                    <div class="flex items-center gap-6">
                        <!-- Avatar with glow -->
                        <div
                            class="h-24 w-24 rounded-full bg-gradient-to-br from-accent to-accent/60 flex items-center justify-center text-4xl font-bold text-accent-foreground shadow-lg relative transition-all duration-300 overflow-hidden group"
                            :class="[{ 'scale-110 shadow-xl': profileHovered }, isOwnProfile ? 'cursor-pointer' : '']"
                            @click="isOwnProfile && handleEditClick()"
                        >
                            <img 
                                v-if="user.profile_photo_path"
                                :src="`/storage/${user.profile_photo_path}`"
                                alt="Profile Photo"
                                class="w-full h-full object-cover"
                            />
                            <span v-else>{{ user.name.charAt(0).toUpperCase() }}</span>
                            <div v-if="isOwnProfile" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                 <span class="text-white text-lg">📸</span>
                             </div>
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-4xl font-bold text-white drop-shadow-lg">
                                    {{ user.name }}
                                </h1>
                                <span class="text-3xl drop-shadow-lg">👑</span>
                            </div>
                            <p class="text-white/90 mb-2 drop-shadow-md">{{ user.email }}</p>
                            <p class="text-sm text-white/85 drop-shadow-md">
                                🎮 Player since {{ user.joinedDate }}
                            </p>
                            <div class="mt-3 pt-3 border-t border-white/30">
                                <p class="text-white font-semibold drop-shadow-md">Level {{ props.stats.level }} Champion</p>
                            </div>
                        </div>
                    </div>

                    <Button 
                        v-if="isOwnProfile"
                        @click="handleEditClick"
                        class="transition-all duration-300 hover:scale-105"
                    >
                        ✏️ Edit Profile
                    </Button>
                </div>
            </div>

            <!-- Bio Section -->
            <div v-if="user.bio" class="rounded-lg border-2 border-sidebar-border/70 dark:border-sidebar-border bg-card p-6">
                <p class="text-foreground">{{ user.bio }}</p>
            </div>

            <!-- XP Progress Bar -->
            <div class="rounded-lg border-2 border-sidebar-border/70 dark:border-sidebar-border bg-card p-6">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-semibold text-foreground">⭐ Experience to Next Level</span>
                        <span class="text-sm text-muted-foreground">{{ props.stats.totalXP % 1000 }} / 1000 XP</span>
                    </div>
                    <div class="relative h-6 w-full overflow-hidden rounded-full bg-muted border border-sidebar-border/50">
                        <div
                            class="h-full bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 transition-all duration-500 rounded-full shadow-lg"
                            :style="{ width: levelProgressAnimated + '%' }"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent"
                            style="opacity: 0.2;"
                            :style="{ width: levelProgressAnimated + '%' }"
                        />
                    </div>
                </div>
            </div>

            <!-- Stats Grid - Interactive -->
            <div class="grid gap-4 md:grid-cols-6">
                <button
                    v-for="(stat, index) in animatedStats"
                    :key="index"
                    @click="createParticle"
                    class="group relative overflow-hidden rounded-lg border-2 border-sidebar-border/70 dark:border-sidebar-border bg-card p-6 text-center transition-all duration-300 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg cursor-pointer hover:scale-105">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-accent/20 to-accent/5 opacity-0 transition-opacity duration-300 group-hover:opacity-100"/>
                    <div class="relative z-10">
                        <div class="mb-3 text-4xl">{{ stat.icon }}</div>
                        <div v-if="stat.isRank" class="text-lg font-bold text-foreground">{{ stats.rankTitle }}</div>
                        <div v-else class="text-3xl font-bold text-foreground">{{ stat.value.toLocaleString() }}</div>
                        <div class="text-xs text-muted-foreground mt-2">{{ stat.label }}{{ stat.suffix }}</div>
                    </div>
                </button>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column: Courses & Progress -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Progress -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border shadow-md">
                        <CardHeader>
                            <CardTitle class="text-foreground flex items-center gap-2">
                                <span>📚</span> Course Progress
                            </CardTitle>
                            <CardDescription>Your learning journey across all enrolled courses</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-6">
                            <div v-if="progress.length === 0" class="text-center py-12">
                                <p class="text-muted-foreground text-lg">🎯 No courses enrolled yet</p>
                                <p class="text-muted-foreground text-sm mt-2">Start your learning journey by enrolling in a course!</p>
                            </div>
                            <div v-for="course in progress" :key="course.courseId" class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-foreground">{{ course.courseName }}</h4>
                                        <p class="text-xs text-muted-foreground">
                                            {{ course.completedLessons }} / {{ course.totalLessons }} lessons completed
                                        </p>
                                    </div>
                                    <span class="text-sm font-bold text-accent">{{ course.progress }}%</span>
                                </div>
                                <div class="h-3 rounded-full bg-muted overflow-hidden border border-sidebar-border/50">
                                    <div
                                        class="h-full bg-gradient-to-r from-accent to-accent/60 transition-all duration-700 rounded-full shadow-lg"
                                        :style="{ width: `${course.progress}%` }"
                                    />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Achievement Summary -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border shadow-md">
                        <CardHeader>
                            <CardTitle class="text-foreground flex items-center gap-2">
                                <span>🏆</span> Achievement Summary
                            </CardTitle>
                            <CardDescription>{{ stats.achievementsUnlocked }} achievements unlocked</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-4 rounded-lg bg-accent/10 border border-accent/20 hover:border-accent/30 transition-all duration-200">
                                    <span class="font-semibold text-foreground">Total Achievements</span>
                                    <span class="text-2xl font-bold text-accent">{{ stats.achievementsUnlocked }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 rounded-lg bg-accent/10 border border-accent/20 hover:border-accent/30 transition-all duration-200">
                                    <span class="font-semibold text-foreground">Success Rate</span>
                                    <span class="text-2xl font-bold text-accent">{{ Math.round((stats.achievementsUnlocked / 20) * 100) }}%</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Achievements & Member Info -->
                <div class="space-y-6">
                    <!-- Achievements -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border shadow-md">
                        <CardHeader>
                            <CardTitle class="text-foreground text-base flex items-center gap-2">
                                <span>⭐</span> Recent Achievements
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="achievements.length === 0" class="text-center py-8">
                                <p class="text-muted-foreground text-sm">🎯 No achievements yet</p>
                                <p class="text-muted-foreground text-xs mt-2">Complete challenges to unlock achievements!</p>
                            </div>
                            <div v-else class="grid grid-cols-3 gap-4">
                                <button
                                    v-for="achievement in achievements.slice(0, 5)"
                                    :key="achievement.id"
                                    class="flex flex-col items-center justify-center p-4 rounded-lg border-2 border-sidebar-border/70 dark:border-sidebar-border bg-card cursor-pointer transition-all duration-200 hover:scale-110 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg"
                                    :title="achievement.description"
                                    @click="handleAchievementClick(achievement)"
                                >
                                    <div class="text-4xl mb-2">{{ achievement.icon }}</div>
                                    <p class="text-xs text-center font-semibold text-foreground">{{ achievement.name }}</p>
                                </button>
                            </div>

                            <Button 
                                v-if="achievements.length > 5" 
                                class="w-full mt-4"
                                variant="outline"
                            >
                                View All Achievements
                            </Button>
                        </CardContent>
                    </Card>

                    <!-- Member Info -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-accent/10 to-accent/5 shadow-md">
                        <CardHeader>
                            <CardTitle class="text-foreground text-sm flex items-center gap-2">
                                <span>👤</span> Member Info
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">📧 Email</p>
                                <p class="text-sm font-medium text-foreground">{{ user.email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">📅 Member Since</p>
                                <p class="text-sm font-medium text-foreground">{{ user.joinedDate }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-muted-foreground mb-2">{{ getStreakEmoji() }} Current Streak</p>
                                <p class="text-sm font-bold text-accent">
                                    {{ stats.currentStreak }} days
                                </p>
                            </div>
                            <Button 
                                v-if="isOwnProfile"
                                @click="handleEditClick"
                                class="w-full mt-4"
                                variant="outline" 
                                size="sm"
                            >
                                ⚙️ Settings
                            </Button>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Achievement Details Dialog -->
        <Dialog v-if="selectedAchievement" :open="!!selectedAchievement" @update:open="(open) => !open && (selectedAchievement = null)">
            <DialogContent class="border-sidebar-border/70 dark:border-sidebar-border">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-3 text-foreground">
                        <span class="text-5xl animate-bounce">{{ selectedAchievement.icon }}</span>
                        {{ selectedAchievement.name }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ selectedAchievement.description }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/30">
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400">✓ Unlocked {{ selectedAchievement.unlockedAt }}</p>
                    </div>
                    <Button class="w-full">
                        🚀 Share Achievement
                    </Button>
                </div>
            </DialogContent>
        </Dialog>


    </AppLayout>
</template>

<style scoped>
@keyframes float-up {
    0% {
        opacity: 1;
        transform: translateY(0) translateX(0);
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translateY(-60px) translateX(20px) scale(0);
    }
}

:deep(input),
:deep(textarea) {
    color-scheme: light dark;
}
</style>
