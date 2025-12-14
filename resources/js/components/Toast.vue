<script setup lang="ts">
import { computed } from 'vue';
import type { Toast } from '@/composables/useToast';

interface Props {
    toast: Toast;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    close: [];
}>();

const toastClasses = computed(() => {
    const baseClasses = 'flex items-start gap-3 w-full min-h-14 px-4 py-3 rounded-lg border backdrop-blur-sm transition-all duration-300';
    
    switch (props.toast.type) {
        case 'achievement':
            return `${baseClasses} bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-900 shadow-lg`;
        case 'quest':
            return `${baseClasses} bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-900 shadow-lg`;
        case 'levelup':
            return `${baseClasses} bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950/40 dark:to-pink-950/40 border-purple-200 dark:border-purple-900 shadow-xl`;
        case 'xp':
            return `${baseClasses} bg-yellow-50 dark:bg-yellow-950/40 border-yellow-200 dark:border-yellow-900 shadow-lg`;
        case 'success':
            return `${baseClasses} bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-900`;
        case 'error':
            return `${baseClasses} bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-900`;
        case 'info':
        default:
            return `${baseClasses} bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-900`;
    }
});

const textClasses = computed(() => {
    switch (props.toast.type) {
        case 'achievement':
            return 'text-amber-900 dark:text-amber-100';
        case 'quest':
            return 'text-green-900 dark:text-green-100';
        case 'levelup':
            return 'text-purple-900 dark:text-purple-100';
        case 'xp':
            return 'text-yellow-900 dark:text-yellow-100';
        case 'success':
            return 'text-green-900 dark:text-green-100';
        case 'error':
            return 'text-red-900 dark:text-red-100';
        case 'info':
        default:
            return 'text-blue-900 dark:text-blue-100';
    }
});

const progressClasses = computed(() => {
    switch (props.toast.type) {
        case 'achievement':
            return 'bg-amber-400';
        case 'quest':
            return 'bg-green-400';
        case 'levelup':
            return 'bg-purple-400';
        case 'xp':
            return 'bg-yellow-400';
        case 'success':
            return 'bg-green-400';
        case 'error':
            return 'bg-red-400';
        case 'info':
        default:
            return 'bg-blue-400';
    }
});

const getDuration = () => {
    return props.toast.duration || 4000;
};
</script>

<template>
    <div :class="toastClasses" :key="toast.id" class="relative">
        <!-- Icon/Avatar -->
        <div class="flex-shrink-0 mt-0.5">
            <div v-if="toast.type === 'achievement'" class="text-2xl">{{ toast.icon || '⭐' }}</div>
            <div v-else-if="toast.type === 'quest'" class="text-2xl">{{ toast.icon || '✓' }}</div>
            <div v-else-if="toast.type === 'levelup'" class="text-3xl animate-bounce">{{ toast.icon || '⚡' }}</div>
            <div v-else-if="toast.type === 'xp'" class="text-xl">{{ toast.icon || '✨' }}</div>
            <div v-else-if="toast.type === 'success'" class="text-xl">✓</div>
            <div v-else-if="toast.type === 'error'" class="text-xl">⚠</div>
            <div v-else class="text-xl">ℹ</div>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <p :class="['font-semibold text-sm', textClasses]">
                {{ toast.title }}
            </p>
            <p v-if="toast.message" :class="['text-xs mt-1 opacity-90', textClasses]">
                {{ toast.message }}
            </p>
        </div>

        <!-- Close Button -->
        <button
            @click="emit('close')"
            class="flex-shrink-0 mt-0.5 text-muted-foreground hover:text-foreground transition-colors"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Progress Bar -->
        <div v-if="toast.duration && toast.duration > 0" class="absolute bottom-0 left-0 right-0 h-1 rounded-b-lg" :class="progressClasses" :style="{
            animation: `shrink ${getDuration()}ms linear forwards`,
        }"></div>
    </div>
</template>

<style scoped>
@keyframes shrink {
    0% {
        transform: scaleX(1);
        transform-origin: left;
    }
    100% {
        transform: scaleX(0);
        transform-origin: left;
    }
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-0.5rem);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>
