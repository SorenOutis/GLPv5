<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useLogo } from '@/composables/useLogo';
import { ref, onMounted, watch } from 'vue';

const isDarkMode = ref(false);
const { hasLogo, logo, getLightLogo, getDarkLogo } = useLogo();

onMounted(() => {
    // Check for saved theme preference or system preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        isDarkMode.value = true;
    } else {
        isDarkMode.value = false;
    }

    // Watch for theme changes
    const observer = new MutationObserver(() => {
        isDarkMode.value = document.documentElement.classList.contains('dark');
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
});
</script>

<template>
    <div v-if="hasLogo && logo" class="flex items-center gap-2">
        <!-- Dynamic Logo Image in Circle -->
        <div class="flex aspect-square size-8 items-center justify-center rounded-full bg-sidebar-primary text-sidebar-primary-foreground flex-shrink-0 overflow-hidden">
            <img
                v-if="isDarkMode && getDarkLogo()"
                :src="getDarkLogo()"
                :alt="logo.name"
                class="h-full w-full object-cover"
            />
            <img
                v-else-if="!isDarkMode && getLightLogo()"
                :src="getLightLogo()"
                :alt="logo.name"
                class="h-full w-full object-cover"
            />
            <img
                v-else-if="getDarkLogo()"
                :src="getDarkLogo()"
                :alt="logo.name"
                class="h-full w-full object-cover"
            />
            <img
                v-else-if="getLightLogo()"
                :src="getLightLogo()"
                :alt="logo.name"
                class="h-full w-full object-cover"
            />
            <AppLogoIcon v-else class="size-5 fill-current text-white dark:text-black" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate leading-tight font-semibold">{{ logo.name }}</span>
        </div>
    </div>
    <div v-else class="flex items-center gap-2">
        <div
            class="flex aspect-square size-8 items-center justify-center rounded-full bg-sidebar-primary text-sidebar-primary-foreground"
        >
            <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate leading-tight font-semibold"
                >LevelUp Academy</span
            >
        </div>
    </div>
</template>
