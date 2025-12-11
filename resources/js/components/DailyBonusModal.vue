<script setup lang="ts">
import { ref, computed } from 'vue';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import axios from 'axios';
import { CheckCircle2, X } from 'lucide-vue-next';

interface Props {
    open: boolean;
    hasReceivedToday: boolean;
    xpAmount: number;
}

const props = withDefaults(defineProps<Props>(), {
    open: false,
    hasReceivedToday: false,
    xpAmount: 20,
});

const emit = defineEmits<{
    'update:open': [value: boolean];
    'bonus-claimed': [result: any];
}>();

const isLoading = ref(false);
const claimError = ref('');
const claimSuccess = ref(false);
const bonusData = ref<any>(null);

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value),
});

const handleClaimBonus = async () => {
    if (props.hasReceivedToday) return;
    
    isLoading.value = true;
    claimError.value = '';
    claimSuccess.value = false;

    try {
        const response = await axios.post('/api/daily-bonus/claim');
        if (response.data.success) {
            claimSuccess.value = true;
            bonusData.value = response.data;
            emit('bonus-claimed', response.data);
            
            // Close modal after 2.5 seconds
            setTimeout(() => {
                isOpen.value = false;
            }, 2500);
        } else {
            claimError.value = response.data.message || 'Failed to claim bonus';
        }
    } catch (error: any) {
        claimError.value = error.response?.data?.message || 'An error occurred while claiming the bonus';
    } finally {
        isLoading.value = false;
    }
};

const handleClose = () => {
    if (!isLoading.value) {
        claimError.value = '';
        claimSuccess.value = false;
        bonusData.value = null;
        isOpen.value = false;
    }
};
</script>

<template>
    <Dialog :open="isOpen" @update:open="handleClose">
        <DialogContent class="max-w-sm border-0 shadow-2xl overflow-hidden">
            <!-- Background gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50"></div>
            
            <!-- Decorative circles -->
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-gradient-to-br from-amber-200 to-orange-200 rounded-full blur-3xl opacity-30"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-gradient-to-tr from-yellow-200 to-amber-200 rounded-full blur-3xl opacity-30"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center gap-6 py-8 px-6">
                <!-- Success State -->
                <transition
                    enter-active-class="animate-in fade-in scale-in duration-300"
                    leave-active-class="animate-out fade-out scale-out duration-200"
                >
                    <div v-if="claimSuccess" class="flex flex-col items-center gap-4 w-full">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-emerald-400 rounded-full blur-2xl opacity-40"></div>
                            <div class="relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-full p-4 border border-green-200">
                                <CheckCircle2 class="w-12 h-12 text-green-600" stroke-width="1.5" />
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">
                                Bonus Claimed!
                            </h3>
                            <p class="text-gray-600 text-sm">
                                You earned <span class="font-semibold text-amber-600">{{ xpAmount }} XP</span>
                            </p>
                            <p class="text-gray-500 text-xs mt-2">
                                Total XP: <span class="font-semibold text-gray-700">{{ bonusData?.total_xp }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Default State -->
                    <div v-else-if="!hasReceivedToday" class="flex flex-col items-center gap-4 w-full">
                        <!-- Icon -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-400 rounded-full blur-2xl opacity-40 animate-pulse"></div>
                            <div class="relative bg-gradient-to-br from-amber-100 to-orange-100 rounded-full p-5 border-2 border-amber-300/50">
                                <span class="text-5xl drop-shadow-lg">🎁</span>
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="text-center space-y-2">
                            <h2 class="text-3xl font-bold bg-gradient-to-r from-amber-600 via-orange-500 to-yellow-600 bg-clip-text text-transparent">
                                Daily Login Bonus
                            </h2>
                            <p class="text-sm text-gray-600">
                                Claim your daily reward and keep your streak going!
                            </p>
                        </div>

                        <!-- XP Display -->
                        <div class="relative mt-2">
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-300 to-orange-300 rounded-2xl blur-lg opacity-30"></div>
                            <div class="relative bg-gradient-to-br from-white to-amber-50 rounded-2xl px-8 py-6 border border-amber-200/60 shadow-lg">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="text-5xl font-bold bg-gradient-to-r from-amber-500 to-orange-500 bg-clip-text text-transparent">
                                        +{{ xpAmount }}
                                    </span>
                                    <span class="text-2xl font-semibold text-gray-700">XP</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Already Claimed State -->
                    <div v-else class="flex flex-col items-center gap-4 w-full">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full blur-2xl opacity-30"></div>
                            <div class="relative bg-gradient-to-br from-blue-50 to-cyan-50 rounded-full p-4 border border-blue-200">
                                <span class="text-4xl">⏰</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent mb-2">
                                Already Claimed
                            </h3>
                            <p class="text-gray-600 text-sm">
                                You've already claimed your bonus today.
                            </p>
                            <p class="text-gray-500 text-xs mt-2">
                                Come back tomorrow for another bonus!
                            </p>
                        </div>
                    </div>
                </transition>

                <!-- Error Message -->
                <transition
                    enter-active-class="animate-in fade-in slide-in-from-top duration-300"
                    leave-active-class="animate-out fade-out slide-out-to-top duration-200"
                >
                    <div v-if="claimError" class="w-full bg-gradient-to-r from-red-50 to-rose-50 border border-red-200/60 rounded-xl p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <X class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="text-red-700 font-semibold text-sm">{{ claimError }}</p>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Buttons -->
                <div class="w-full flex gap-3 mt-2">
                    <Button
                        v-if="!hasReceivedToday && !claimSuccess"
                        @click="handleClaimBonus"
                        :disabled="isLoading"
                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold py-2.5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 disabled:opacity-75"
                    >
                        <span v-if="!isLoading" class="flex items-center justify-center gap-2">
                            <span>Claim Bonus</span>
                        </span>
                        <span v-else class="flex items-center justify-center gap-2">
                            <span class="inline-block animate-spin">⚡</span>
                            <span>Claiming...</span>
                        </span>
                    </Button>
                    <Button
                        @click="handleClose"
                        :disabled="isLoading"
                        class="flex-1 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 disabled:opacity-75"
                    >
                        {{ claimSuccess || hasReceivedToday ? 'Close' : 'Maybe Later' }}
                    </Button>
                </div>

                <!-- Streak Reminder -->
                <div class="text-center text-xs text-gray-500 mt-2">
                    <p>💪 Keep your streak alive. Log in daily for rewards!</p>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
