<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';

interface LeaderboardEntry {
    id: number;
    rank: number;
    name: string;
    xp: number;
    level: number;
    badge: string;
    isUser?: boolean;
}

interface Props {
    leaderboard: LeaderboardEntry[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    profileClick: [userId: number];
}>();

const hoveredId = ref<number | null>(null);
const animatedXP = ref<Record<number, number>>({});

const topThree = computed(() => props.leaderboard.slice(0, 3));
const restLeaderboard = computed(() => props.leaderboard.slice(3, 10));

const getMedalIcon = (rank: number): string => {
    switch (rank) {
        case 1:
            return '🥇';
        case 2:
            return '🥈';
        case 3:
            return '🥉';
        default:
            return `#${rank}`;
    }
};

const getPodiumHeight = (rank: number): string => {
    switch (rank) {
        case 1:
            return 'h-32';
        case 2:
            return 'h-28';
        case 3:
            return 'h-24';
        default:
            return 'h-20';
    }
};

const getPodiumColor = (rank: number): string => {
    switch (rank) {
        case 1:
            return 'bg-gradient-to-b from-yellow-400 to-yellow-600';
        case 2:
            return 'bg-gradient-to-b from-gray-300 to-gray-500';
        case 3:
            return 'bg-gradient-to-b from-orange-400 to-orange-600';
        default:
            return 'bg-gradient-to-b from-blue-400 to-blue-600';
    }
};

const animateXP = (id: number) => {
    const entry = props.leaderboard.find(e => e.id === id);
    if (!entry) return;

    animatedXP.value[id] = 0;
    const targetXP = entry.xp;
    const duration = 1500;
    const startTime = Date.now();

    const animate = () => {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);

        animatedXP.value[id] = Math.floor(targetXP * progress);

        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };

    animate();
};

const getDisplayXP = (id: number, xp: number): string => {
    return (animatedXP.value[id] ?? xp).toLocaleString();
};

const handleHover = (id: number) => {
    hoveredId.value = id;
    animateXP(id);
};

const handleProfileClick = (userId: number) => {
    emit('profileClick', userId);
    router.visit(`/users/${userId}`);
};

onMounted(() => {
    props.leaderboard.forEach(entry => {
        animatedXP.value[entry.id] = entry.xp;
    });
});
</script>

<template>
    <Card class="border-sidebar-border/70 dark:border-sidebar-border transition-all duration-200 hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg col-span-full">
        <CardHeader>
            <CardTitle>Leaderboard</CardTitle>
            <CardDescription>Top learners this month</CardDescription>
        </CardHeader>
        <CardContent class="space-y-6 flex flex-col">
            <!-- 3D Podium -->
            <div v-if="topThree.length > 0" class="space-y-4 flex-shrink-0">
                <!-- Podium Visualization -->
                <div class="flex items-end justify-center gap-2 px-4 py-8 bg-gradient-to-b from-accent/5 to-accent/10 rounded-lg">
                    <!-- 2nd Place -->
                    <div v-if="topThree[1]" class="flex flex-col items-center flex-1">
                        <div
                            @mouseenter="handleHover(topThree[1].id)"
                            @click="handleProfileClick(topThree[1].id)"
                            class="w-full cursor-pointer transition-transform duration-300 hover:scale-105"
                        >
                            <!-- Medal Badge -->
                            <div class="flex justify-center mb-2">
                                <div class="text-4xl drop-shadow-lg">{{ getMedalIcon(2) }}</div>
                            </div>

                            <!-- Podium Block -->
                            <div :class="['w-full rounded-t-lg shadow-2xl transition-all duration-300', getPodiumColor(2), getPodiumHeight(2)]" style="transform-origin: bottom; perspective: 1000px;">
                                <!-- 3D Effect -->
                                <div class="h-full flex flex-col items-center justify-center text-white drop-shadow-lg p-2">
                                    <p class="font-bold text-lg">{{ topThree[1].rank }}</p>
                                    <p class="text-xs text-center truncate max-w-full">{{ topThree[1].name }}</p>
                                    <p class="text-lg font-bold mt-1">{{ getDisplayXP(topThree[1].id, topThree[1].xp) }}</p>
                                </div>
                            </div>

                            <!-- Shadow -->
                            <div class="h-1 bg-black/10 rounded-b-lg"></div>
                        </div>

                        <!-- Details -->
                        <div class="mt-3 text-center text-xs">
                            <p class="font-medium">Lvl {{ topThree[1].level }}</p>
                        </div>
                    </div>

                    <!-- 1st Place (Center & Tallest) -->
                    <div v-if="topThree[0]" class="flex flex-col items-center flex-1">
                        <div
                            @mouseenter="handleHover(topThree[0].id)"
                            @click="handleProfileClick(topThree[0].id)"
                            class="w-full cursor-pointer transition-transform duration-300 hover:scale-105"
                        >
                            <!-- Crown Badge -->
                            <div class="flex justify-center mb-2">
                                <div class="text-5xl drop-shadow-lg animate-bounce">{{ getMedalIcon(1) }}</div>
                            </div>

                            <!-- Podium Block - Tallest -->
                            <div :class="['w-full rounded-t-lg shadow-2xl transition-all duration-300', getPodiumColor(1), 'h-40']" style="transform-origin: bottom; perspective: 1000px; box-shadow: 0 20px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2);">
                                <!-- 3D Effect with inner highlight -->
                                <div class="absolute top-0 left-0 right-0 h-1/3 bg-gradient-to-b from-white/20 to-transparent rounded-t-lg"></div>
                                <div class="h-full flex flex-col items-center justify-center text-white drop-shadow-lg p-2 relative z-10">
                                    <p class="font-bold text-2xl">{{ topThree[0].rank }}</p>
                                    <p class="text-sm text-center font-semibold truncate max-w-full">{{ topThree[0].name }}</p>
                                    <p class="text-2xl font-bold mt-2">{{ getDisplayXP(topThree[0].id, topThree[0].xp) }}</p>
                                </div>
                            </div>

                            <!-- Shadow -->
                            <div class="h-1 bg-black/20 rounded-b-lg"></div>
                        </div>

                        <!-- Details -->
                        <div class="mt-3 text-center text-xs">
                            <p class="font-medium">Lvl {{ topThree[0].level }}</p>
                        </div>
                    </div>

                    <!-- 3rd Place -->
                    <div v-if="topThree[2]" class="flex flex-col items-center flex-1">
                        <div
                            @mouseenter="handleHover(topThree[2].id)"
                            @click="handleProfileClick(topThree[2].id)"
                            class="w-full cursor-pointer transition-transform duration-300 hover:scale-105"
                        >
                            <!-- Medal Badge -->
                            <div class="flex justify-center mb-2">
                                <div class="text-4xl drop-shadow-lg">{{ getMedalIcon(3) }}</div>
                            </div>

                            <!-- Podium Block -->
                            <div :class="['w-full rounded-t-lg shadow-2xl transition-all duration-300', getPodiumColor(3), getPodiumHeight(3)]" style="transform-origin: bottom; perspective: 1000px;">
                                <!-- 3D Effect -->
                                <div class="h-full flex flex-col items-center justify-center text-white drop-shadow-lg p-2">
                                    <p class="font-bold text-lg">{{ topThree[2].rank }}</p>
                                    <p class="text-xs text-center truncate max-w-full">{{ topThree[2].name }}</p>
                                    <p class="text-lg font-bold mt-1">{{ getDisplayXP(topThree[2].id, topThree[2].xp) }}</p>
                                </div>
                            </div>

                            <!-- Shadow -->
                            <div class="h-1 bg-black/10 rounded-b-lg"></div>
                        </div>

                        <!-- Details -->
                        <div class="mt-3 text-center text-xs">
                            <p class="font-medium">Lvl {{ topThree[2].level }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rest of Leaderboard - Scrollable -->
            <div v-if="restLeaderboard.length > 0" class="flex-1 overflow-y-auto min-h-0 border-t pt-4 px-1">
                <div class="space-y-2">
                    <div
                        v-for="leader in restLeaderboard"
                        :key="leader.rank"
                        @mouseenter="handleHover(leader.id)"
                        @click="handleProfileClick(leader.id)"
                        :class="[
                            'flex items-center gap-3 p-3 rounded-lg transition-all duration-200 cursor-pointer relative',
                            hoveredId === leader.id
                                ? 'bg-accent/20 border border-accent shadow-lg z-10'
                                : leader.isUser
                                ? 'bg-accent/10 border border-accent/50'
                                : 'bg-muted/50 border border-transparent hover:bg-accent/5 hover:border-accent/30'
                        ]"
                    >
                        <!-- Rank Badge -->
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 text-white font-bold shadow-md">
                            #{{ leader.rank }}
                        </div>

                        <!-- User Info -->
                        <div class="flex-1 min-w-0">
                            <p :class="['text-sm font-semibold truncate', leader.isUser ? 'text-accent font-bold' : 'text-foreground']">
                                {{ leader.name }}
                            </p>
                            <p class="text-xs text-muted-foreground">Lvl {{ leader.level }}</p>
                        </div>

                        <!-- XP with Animation -->
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                {{ getDisplayXP(leader.id, leader.xp) }}
                            </p>
                            <p class="text-xs text-muted-foreground">XP</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-8">
                <p class="text-muted-foreground text-sm">No leaderboard data yet</p>
            </div>
        </CardContent>
    </Card>
</template>

<style scoped>
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-0.5rem);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
