<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { CheckCircle2, ChevronRight, AlertCircle } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';

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
const attemptCount = ref(0);
const maxAttempts = 3;

const isVerified = computed(() => sliderPosition.value >= 100);
const isFailed = computed(() => attemptCount.value >= maxAttempts);

const handleMouseDown = () => {
    isDragging.value = true;
    attemptCount.value = 0;
};

const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging.value || !sliderRef.value) return;

    const slider = sliderRef.value;
    const rect = slider.getBoundingClientRect();

    let newPosition = e.clientX - rect.left;
    newPosition = Math.max(0, Math.min(newPosition, rect.width));

    sliderPosition.value = (newPosition / rect.width) * 100;
};

const handleMouseUp = () => {
    isDragging.value = false;
    if (!isVerified.value) {
        // Reset slider if not fully dragged
        setTimeout(() => {
            attemptCount.value += 1;
            sliderPosition.value = 0;
        }, 200);
    }
};

const handleTouchStart = () => {
    isDragging.value = true;
    attemptCount.value = 0;
};

const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging.value || !sliderRef.value) return;

    const slider = sliderRef.value;
    const rect = slider.getBoundingClientRect();

    let newPosition = e.touches[0].clientX - rect.left;
    newPosition = Math.max(0, Math.min(newPosition, rect.width));

    sliderPosition.value = (newPosition / rect.width) * 100;
};

const handleTouchEnd = () => {
    isDragging.value = false;
    if (!isVerified.value) {
        setTimeout(() => {
            attemptCount.value += 1;
            sliderPosition.value = 0;
        }, 200);
    }
};

onMounted(() => {
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
    document.addEventListener('touchmove', handleTouchMove);
    document.addEventListener('touchend', handleTouchEnd);
});

onUnmounted(() => {
    document.removeEventListener('mousemove', handleMouseMove);
    document.removeEventListener('mouseup', handleMouseUp);
    document.removeEventListener('touchmove', handleTouchMove);
    document.removeEventListener('touchend', handleTouchEnd);
});

const handleClose = () => {
    resetSlider();
    emit('update:modelValue', false);
};

const handleContinue = async () => {
    emit('verified');
};

const resetSlider = () => {
    sliderPosition.value = 0;
    attemptCount.value = 0;
};
</script>

<template>
    <Dialog :open="modelValue" @update:open="handleClose">
        <DialogContent
            class="max-w-md border-0 shadow-2xl overflow-hidden p-0 rounded-2xl bg-white dark:bg-slate-900">
            <!-- Gradient background -->
            <div
                class="absolute inset-0 bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800"></div>

            <!-- Decorative elements -->
            <div
                class="absolute -top-24 -right-24 w-48 h-48 bg-gradient-to-br from-amber-300 to-orange-300 rounded-full blur-3xl opacity-20 dark:opacity-10"></div>
            <div
                class="absolute -bottom-24 -left-24 w-48 h-48 bg-gradient-to-tr from-yellow-300 to-amber-300 rounded-full blur-3xl opacity-20 dark:opacity-10"></div>

            <!-- Content -->
            <div class="relative z-10 p-8 space-y-6">
                <!-- Close button -->
                <button @click="handleClose"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Header -->
                <div class="text-center space-y-2">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-amber-600 via-orange-500 to-yellow-600 bg-clip-text text-transparent">
                        Verify Your Identity
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Please complete the slider to continue
                    </p>
                </div>

                <!-- Status indicators -->
                <div v-if="!isVerified && !isFailed" class="flex justify-center gap-1">
                    <div v-for="i in maxAttempts" :key="i"
                        :class="[
                            'h-1.5 rounded-full transition-all duration-300',
                            i <= attemptCount
                                ? 'bg-gray-300 dark:bg-gray-600 w-2'
                                : 'bg-gradient-to-r from-amber-400 to-orange-400 w-3'
                        ]"></div>
                </div>

                <!-- Slider Container -->
                <div class="space-y-3">
                    <!-- Main Slider -->
                    <div v-if="!isFailed"
                        ref="sliderRef"
                        class="relative w-full h-14 bg-gradient-to-r from-gray-100 to-gray-50 dark:from-slate-700 dark:to-slate-600 rounded-full cursor-grab active:cursor-grabbing overflow-hidden border-2 border-gray-200 dark:border-slate-600 shadow-md hover:shadow-lg transition-shadow duration-300">

                        <!-- Success Track -->
                        <div v-if="isVerified"
                            class="absolute inset-0 bg-gradient-to-r from-green-400 via-emerald-400 to-green-500 dark:from-green-600 dark:via-emerald-500 dark:to-green-600 transition-all duration-500"></div>

                        <!-- Progress Track -->
                        <div v-else
                            :style="{ width: `${sliderPosition}%` }"
                            class="absolute inset-y-0 left-0 bg-gradient-to-r from-amber-400 via-orange-400 to-yellow-400 dark:from-amber-500 dark:via-orange-500 dark:to-yellow-500 transition-all duration-75 ease-out"></div>

                        <!-- Handle -->
                         <div ref="handleRef"
                             @mousedown="handleMouseDown"
                             @touchstart="handleTouchStart"
                             :style="{ left: `${sliderPosition}%`, transform: `translateX(-50%) translateY(-50%)` }"
                             :class="[
                                 'absolute top-1/2 w-12 h-12 rounded-full shadow-lg flex items-center justify-center cursor-grab active:cursor-grabbing transition-all duration-300 hover:scale-110 z-20',
                                isVerified
                                    ? 'bg-gradient-to-br from-green-400 to-emerald-500 dark:from-green-500 dark:to-emerald-600'
                                    : 'bg-gradient-to-br from-white to-gray-50 dark:from-slate-300 dark:to-slate-200'
                            ]">
                            <!-- Arrow Icon -->
                            <ChevronRight v-if="!isVerified"
                                :size="20"
                                class="text-orange-600 dark:text-orange-500 transition-all duration-300"
                                :class="{ 'opacity-0 translate-x-2': isVerified }" />
                            <!-- Check Icon -->
                            <CheckCircle2 v-else
                                :size="24"
                                class="text-white absolute transition-all duration-300"
                                stroke-width="1.5" />
                        </div>

                        <!-- Text Label -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <span v-if="!isVerified"
                                class="text-gray-600 dark:text-gray-400 font-semibold text-sm">
                                Slide to Verify
                            </span>
                            <span v-else class="text-white font-bold">
                                Verified!
                            </span>
                        </div>
                    </div>

                    <!-- Failed State -->
                    <div v-else
                        class="space-y-4 text-center">
                        <div class="flex justify-center">
                            <div class="relative">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-red-400 to-rose-400 rounded-full blur-lg opacity-40"></div>
                                <div
                                    class="relative bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-950 dark:to-rose-950 rounded-full p-3 border border-red-200 dark:border-red-800">
                                    <AlertCircle class="w-8 h-8 text-red-600 dark:text-red-400" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-red-600 dark:text-red-400 font-semibold">
                                Too Many Attempts
                            </p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                Please try again later or contact support.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Continue Button -->
                <Transition
                    enter-active-class="animate-in fade-in scale-in duration-300"
                    leave-active-class="animate-out fade-out scale-out duration-200">
                    <Button v-if="isVerified"
                        @click="handleContinue"
                        class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 dark:from-green-600 dark:to-emerald-600 dark:hover:from-green-700 dark:hover:to-emerald-700 text-white font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 text-base">
                        Continue
                    </Button>
                </Transition>

                <!-- Security message -->
                <div class="text-center text-xs text-gray-500 dark:text-gray-400">
                    <p>🔒 This verification helps keep your account secure</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
