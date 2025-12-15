<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    streak: number;
    size?: 'sm' | 'md' | 'lg' | 'xl';
    animated?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    animated: true,
});

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'h-6 w-6',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
        xl: 'h-16 w-16',
    };
    return sizes[props.size];
});

const flameColor = computed(() => {
    if (props.streak >= 30) return '#ea580c';
    if (props.streak >= 14) return '#f59e0b';
    if (props.streak >= 7) return '#eab308';
    return '#a3a3a3';
});

const flameIntensity = computed(() => {
    if (props.streak >= 30) return 0.8;
    if (props.streak >= 14) return 0.6;
    if (props.streak >= 7) return 0.4;
    return 0.2;
});
</script>

<template>
    <div :class="[sizeClasses, 'relative inline-flex items-center justify-center']">
        <!-- Glow effect (animated) -->
        <div
            v-if="animated"
            :class="[sizeClasses, 'absolute rounded-full animate-pulse']"
            :style="{
                backgroundColor: flameColor,
                opacity: flameIntensity * 0.3,
                filter: 'blur(8px)',
            }"
        />

        <!-- Flame SVG -->
        <svg
            :class="[sizeClasses, 'relative z-10', { 'animate-bounce': animated && streak > 0 }]"
            viewBox="0 0 24 24"
            fill="currentColor"
            :style="{ color: flameColor }"
        >
            <!-- Flame shape - looks like fire -->
            <path
                d="M12 2c0 0-3 4-3 7 0 2.21 1.79 4 4 4s4-1.79 4-4c0-3-3-7-3-7z"
            />
            <!-- Flame middle -->
            <path
                d="M12 6c0 0-2 2.5-2 4 0 1.66 1.34 3 3 3s3-1.34 3-3c0-1.5-2-4-2-4z"
                opacity="0.8"
            />
            <!-- Flame tip -->
            <path
                d="M12 9c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"
                opacity="0.6"
            />
        </svg>

        <!-- Animated particles (for higher streaks) -->
        <div v-if="animated && streak >= 7" class="absolute inset-0">
            <div
                class="absolute h-1 w-1 rounded-full animate-ping"
                :style="{ backgroundColor: flameColor, top: '0%', left: '50%' }"
            />
            <div
                class="absolute h-1 w-1 rounded-full animate-ping"
                :style="{
                    backgroundColor: flameColor,
                    top: '25%',
                    right: '0%',
                    animationDelay: '0.3s',
                }"
            />
            <div
                class="absolute h-1 w-1 rounded-full animate-ping"
                :style="{
                    backgroundColor: flameColor,
                    bottom: '0%',
                    left: '20%',
                    animationDelay: '0.6s',
                }"
            />
        </div>
    </div>
</template>

<style scoped>
@keyframes flame-flicker {
    0%,
    100% {
        filter: brightness(1);
    }
    50% {
        filter: brightness(1.2);
    }
}

.animate-flame {
    animation: flame-flicker 0.8s ease-in-out infinite;
}
</style>
