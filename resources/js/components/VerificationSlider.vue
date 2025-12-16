<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
    modelValue: boolean;
    redirectTo?: 'login' | 'register';
}

const props = withDefaults(defineProps<Props>(), {
    redirectTo: 'login',
});

const emit = defineEmits<{
    'update:modelValue': [value: boolean];
    'verified': [];
}>();

const isDragging = ref(false);
const sliderPosition = ref(0);
const sliderRef = ref<HTMLElement>();
const handleRef = ref<HTMLElement>();

const isVerified = computed(() => sliderPosition.value >= 90);

const handleMouseDown = () => {
    isDragging.value = true;
};

const handleMouseUp = () => {
    isDragging.value = false;
    if (!isVerified.value) {
        // Reset slider if not fully dragged
        setTimeout(() => {
            sliderPosition.value = 0;
        }, 200);
    }
};

const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging.value || !sliderRef.value) return;

    const slider = sliderRef.value;
    const rect = slider.getBoundingClientRect();
    const handleWidth = 96; // w-24 = 96px
    const maxTravel = rect.width - handleWidth;

    let newPosition = e.clientX - rect.left - handleWidth / 2;
    newPosition = Math.max(0, Math.min(newPosition, maxTravel));

    sliderPosition.value = (newPosition / maxTravel) * 100;
};

const handleTouchStart = () => {
    isDragging.value = true;
};

const handleTouchEnd = () => {
    isDragging.value = false;
    if (!isVerified.value) {
        setTimeout(() => {
            sliderPosition.value = 0;
        }, 200);
    }
};

const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging.value || !sliderRef.value) return;

    const slider = sliderRef.value;
    const rect = slider.getBoundingClientRect();
    const handleWidth = 96;
    const maxTravel = rect.width - handleWidth;

    let newPosition = e.touches[0].clientX - rect.left - handleWidth / 2;
    newPosition = Math.max(0, Math.min(newPosition, maxTravel));

    sliderPosition.value = (newPosition / maxTravel) * 100;
};

const handleClose = () => {
    emit('update:modelValue', false);
    sliderPosition.value = 0;
};

const handleContinue = async () => {
    emit('verified');
    // The parent component will handle navigation
};
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="handleClose" />

                <!-- Modal Content -->
                <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 max-w-md w-full">
                    <!-- Close Button -->
                    <button @click="handleClose"
                        class="absolute top-5 right-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Content -->
                    <div class="text-center space-y-6">
                        <!-- Title -->
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                                Verification Required
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-2 text-lg">
                                Please slide to confirm you're human.
                            </p>
                        </div>

                        <!-- Slider -->
                        <div class="pt-4">
                            <div ref="sliderRef" @mousemove="handleMouseMove" @mouseup="handleMouseUp"
                                @mouseleave="handleMouseUp" @touchmove="handleTouchMove" @touchend="handleTouchEnd"
                                class="relative w-full h-16 bg-gradient-to-r from-blue-100 to-blue-50 dark:from-gray-700 dark:to-gray-600 rounded-full cursor-grab active:cursor-grabbing overflow-hidden border border-blue-200 dark:border-gray-600">

                                <!-- Slider Track Background -->
                                <div v-if="isVerified"
                                    class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-300 dark:from-green-600 dark:to-green-500 transition-all duration-300" />

                                <!-- Handle -->
                                <div ref="handleRef" @mousedown="handleMouseDown" @touchstart="handleTouchStart"
                                    :style="{ transform: `translateX(${sliderPosition}%)` }"
                                    class="absolute top-1/2 left-0 -translate-y-1/2 w-24 h-14 rounded-full bg-gradient-to-r from-blue-600 to-blue-500 dark:from-blue-500 dark:to-blue-600 shadow-lg flex items-center justify-center cursor-grab active:cursor-grabbing transition-colors duration-300"
                                    :class="{ 'bg-gradient-to-r from-green-600 to-green-500 dark:from-green-500 dark:to-green-600': isVerified }">
                                    <!-- Arrow Icon -->
                                    <svg class="w-6 h-6 text-white transition-all duration-300"
                                        :class="{ 'translate-x-2 opacity-0': isVerified }" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M13.5 4.5L19 10m0 0l-5.5 5.5m5.5-5.5h-16" />
                                    </svg>
                                    <!-- Check Icon -->
                                    <svg v-if="isVerified"
                                        class="w-6 h-6 text-white absolute transition-all duration-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                <!-- Text Label -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <span v-if="!isVerified" class="text-gray-600 dark:text-gray-300 font-medium">
                                        Slide to Verify
                                    </span>
                                    <span v-else class="text-green-600 dark:text-green-400 font-bold">
                                        Verified!
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Continue Button -->
                        <Transition name="fade">
                            <button v-if="isVerified" @click="handleContinue"
                                class="w-full px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-green-600 to-green-500 dark:from-green-600 dark:to-green-500 text-white hover:shadow-lg hover:shadow-green-500/50 transition-all duration-300">
                                Continue
                            </button>
                        </Transition>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from :deep(.relative),
.modal-leave-to :deep(.relative) {
    transform: scale(0.95);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
