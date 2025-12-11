<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { useLogo } from '@/composables/useLogo';
import { useAppearance } from '@/composables/useAppearance';

const { hasLogo, logo, getLightLogo, getDarkLogo } = useLogo();
const { appearance, updateAppearance } = useAppearance();

const isDarkMode = computed(() => {
    if (appearance.value === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    return appearance.value === 'dark';
});

const toggleTheme = () => {
    if (isDarkMode.value) {
        updateAppearance('light');
    } else {
        updateAppearance('dark');
    }
};

onMounted(() => {
    const savedAppearance = localStorage.getItem('appearance');
    if (savedAppearance) {
        updateAppearance(savedAppearance as 'light' | 'dark' | 'system');
    }
});
</script>

<template>
    <Head title="Terms of Service" />
    <div class="min-h-screen bg-background text-foreground transition-colors duration-500 overflow-hidden">
        <!-- Animated Background Gradient Orbs -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute w-80 h-80 rounded-full blur-3xl opacity-5 bg-primary top-20 -left-40 animate-blob" />
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-5 bg-accent bottom-20 -right-40 animate-blob"
                style="animation-delay: 2s"
            />
        </div>

        <!-- Header with Theme Toggle -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-background/50 border-b border-border transition-colors duration-500">
            <nav class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 group cursor-pointer">
                    <!-- Dynamic Logo or Fallback -->
                    <div v-if="hasLogo && logo" class="h-10 w-10 rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-shadow duration-300 flex-shrink-0">
                        <img
                            v-if="isDarkMode && getDarkLogo()"
                            :src="getDarkLogo()"
                            :alt="logo.name"
                            class="h-full w-full object-cover group-hover:opacity-80 transition-opacity duration-300"
                        />
                        <img
                            v-else-if="!isDarkMode && getLightLogo()"
                            :src="getLightLogo()"
                            :alt="logo.name"
                            class="h-full w-full object-cover group-hover:opacity-80 transition-opacity duration-300"
                        />
                        <img
                            v-else-if="getDarkLogo()"
                            :src="getDarkLogo()"
                            :alt="logo.name"
                            class="h-full w-full object-cover group-hover:opacity-80 transition-opacity duration-300"
                        />
                        <img
                            v-else-if="getLightLogo()"
                            :src="getLightLogo()"
                            :alt="logo.name"
                            class="h-full w-full object-cover group-hover:opacity-80 transition-opacity duration-300"
                        />
                        <div
                            v-else
                            class="h-full w-full bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 dark:from-blue-400 dark:via-purple-500 dark:to-pink-500 flex items-center justify-center"
                        >
                            <span class="text-white font-bold text-lg">✦</span>
                        </div>
                    </div>
                    <!-- Fallback Logo -->
                    <div
                        v-else
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 dark:from-blue-400 dark:via-purple-500 dark:to-pink-500 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300"
                    >
                        <span class="text-white font-bold text-lg">✦</span>
                    </div>
                    <span
                        class="font-bold text-xl bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent"
                    >
                        LevelUp Academy
                    </span>
                </Link>

                <!-- Theme Toggle -->
                <button
                    @click="toggleTheme"
                    class="p-2.5 rounded-lg bg-secondary text-secondary-foreground hover:bg-muted transition-all duration-300 border border-border"
                    :aria-label="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <svg v-if="!isDarkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm5.657-9.193a1 1 0 00-1.414 0l-.707.707A1 1 0 005.05 6.464l.707-.707a1 1 0 011.414 0zm0 18.186a1 1 0 001.414 0l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 000 1.414zM5.05 6.464a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="flex min-h-[calc(100vh-80px)] flex-col items-center justify-start p-6 md:p-10">
            <div class="w-full max-w-4xl">
                <!-- Header -->
                <div class="mb-12">
                    <h1 class="text-4xl font-bold text-primary mb-4">Terms of Service</h1>
                    <p class="text-muted-foreground">Last updated: December 2024</p>
                </div>

                <!-- Content -->
                <div class="prose dark:prose-invert max-w-none">
                    <div class="bg-card border border-border rounded-xl p-8 space-y-8">
                        <!-- Section 1 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">1. Agreement to Terms</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                By accessing and using LevelUp Academy, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
                            </p>
                        </section>

                        <!-- Section 2 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">2. Use License</h2>
                            <p class="text-muted-foreground leading-relaxed mb-4">
                                Permission is granted to temporarily download one copy of the materials (information or software) on LevelUp Academy for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-muted-foreground">
                                <li>Modifying or copying the materials</li>
                                <li>Using the materials for any commercial purpose or for any public display</li>
                                <li>Attempting to decompile or reverse engineer any software contained on the site</li>
                                <li>Removing any copyright or other proprietary notations from the materials</li>
                                <li>Transferring the materials to another person or "mirroring" the materials on any other server</li>
                            </ul>
                        </section>

                        <!-- Section 3 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">3. Disclaimer</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                The materials on LevelUp Academy are provided "as is". We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
                            </p>
                        </section>

                        <!-- Section 4 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">4. Limitations</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                In no event shall LevelUp Academy or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on LevelUp Academy, even if we or an authorized representative has been notified orally or in writing of the possibility of such damage.
                            </p>
                        </section>

                        <!-- Section 5 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">5. Accuracy of Materials</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                The materials appearing on LevelUp Academy could include technical, typographical, or photographic errors. LevelUp Academy does not warrant that any of the materials on the site are accurate, complete, or current. We may make changes to the materials contained on the site at any time without notice.
                            </p>
                        </section>

                        <!-- Section 6 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">6. Links</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                LevelUp Academy has not reviewed all of the sites linked to its Internet web site and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by LevelUp Academy of the site. Use of any such linked website is at the user's own risk.
                            </p>
                        </section>

                        <!-- Section 7 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">7. Modifications</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                LevelUp Academy may revise these terms of service for the site at any time without notice. By using this site, you are agreeing to be bound by the then current version of these terms of service.
                            </p>
                        </section>

                        <!-- Section 8 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">8. Governing Law</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which LevelUp Academy operates, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.
                            </p>
                        </section>

                        <!-- Section 9 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">9. Contact Information</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                If you have any questions about these Terms of Service, please contact us at support@levelupacademy.com
                            </p>
                        </section>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="mt-8 text-center">
                    <Link
                        href="/"
                        class="text-primary hover:underline font-medium"
                    >
                        ← Back to Home
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}
</style>
