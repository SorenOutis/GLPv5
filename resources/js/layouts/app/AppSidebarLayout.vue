<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import AnimationContainer from '@/components/AnimationContainer.vue';
import type { BreadcrumbItemType } from '@/types';
import { ref, provide } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

interface ModalState {
    isOpen: boolean;
    title: string;
    message: string;
}

const logoutModal = ref<ModalState>({
    isOpen: false,
    title: 'Logging out...',
    message: 'Please wait while we log you out'
});

provide('logoutModal', logoutModal);

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <!-- Logout Modal Overlay -->
        <div
            v-if="logoutModal.isOpen"
            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm transition-opacity duration-300"
        >
            <!-- Modal -->
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div
                    class="relative w-full max-w-sm bg-card border border-border rounded-2xl shadow-2xl p-8 animate-scale-in"
                >
                    <!-- Spinner -->
                    <div class="flex justify-center mb-6">
                        <div class="relative w-16 h-16">
                            <div class="absolute inset-0 rounded-full border-4 border-border/30" />
                            <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-primary border-r-primary animate-spin" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="text-center space-y-3">
                        <h2 class="text-2xl font-bold text-foreground">
                            {{ logoutModal.title }}
                        </h2>
                        <p class="text-sm text-muted-foreground">
                            {{ logoutModal.message }}
                        </p>
                    </div>

                    <!-- Loading dots animation -->
                    <div class="flex justify-center gap-1.5 mt-6">
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0s" />
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.2s" />
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.4s" />
                    </div>
                </div>
            </div>
        </div>

        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>

        <!-- Global animation container -->
        <AnimationContainer />
    </AppShell>
</template>

<style scoped>
@keyframes scale-in {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-scale-in {
    animation: scale-in 1s ease-out;
}
</style>
