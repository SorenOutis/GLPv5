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
    <Head title="Privacy Policy" />
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
                    <h1 class="text-4xl font-bold text-primary mb-4">Privacy Policy</h1>
                    <p class="text-muted-foreground">Last updated: December 2024</p>
                </div>

                <!-- Content -->
                <div class="prose dark:prose-invert max-w-none">
                    <div class="bg-card border border-border rounded-xl p-8 space-y-8">
                        <!-- Section 1 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">1. Introduction</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                LevelUp Academy ("we" or "us" or "our") operates the platform. This page informs you of our policies regarding the collection, use, and disclosure of personal data when you use our service and the choices you have associated with that data.
                            </p>
                        </section>

                        <!-- Section 2 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">2. Information Collection and Use</h2>
                            <p class="text-muted-foreground leading-relaxed mb-4">
                                We collect several different types of information for various purposes to provide and improve our service to you.
                            </p>
                            <h3 class="text-xl font-semibold text-foreground mb-2">Types of Data Collected:</h3>
                            <ul class="list-disc list-inside space-y-2 text-muted-foreground">
                                <li><strong>Personal Data:</strong> Email address, first name, last name, phone number, address, cookies and usage data</li>
                                <li><strong>Usage Data:</strong> Pages visited, time spent on pages, links clicked, and other interaction data</li>
                                <li><strong>Device Information:</strong> Browser type, IP address, operating system, and device identifiers</li>
                            </ul>
                        </section>

                        <!-- Section 3 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">3. Use of Data</h2>
                            <p class="text-muted-foreground leading-relaxed mb-4">
                                LevelUp Academy uses the collected data for various purposes:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-muted-foreground">
                                <li>To provide and maintain our service</li>
                                <li>To notify you about changes to our service</li>
                                <li>To allow you to participate in interactive features of our service</li>
                                <li>To provide customer support</li>
                                <li>To gather analysis or valuable information to improve our service</li>
                                <li>To monitor the usage of our service</li>
                                <li>To detect, prevent and address technical issues</li>
                            </ul>
                        </section>

                        <!-- Section 4 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">4. Security of Data</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                The security of your data is important to us, but remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your personal data, we cannot guarantee its absolute security.
                            </p>
                        </section>

                        <!-- Section 5 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">5. Cookies</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                We use cookies and similar tracking technologies to track activity on our service and hold certain information. Cookies are files with small amounts of data which may include an anonymous unique identifier.
                            </p>
                        </section>

                        <!-- Section 6 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">6. Third-Party Links</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                Our service may contain links to other sites that are not operated by us. This Privacy Policy does not apply to third-party websites, and we are not responsible for their privacy practices. We encourage you to review the privacy policies of any third-party sites before providing your personal information.
                            </p>
                        </section>

                        <!-- Section 7 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">7. Data Retention</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                We will retain your personal data only for as long as necessary for the purposes set out in this Privacy Policy. We will retain and use your personal data to the extent necessary to comply with our legal obligations.
                            </p>
                        </section>

                        <!-- Section 8 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">8. Your Rights</h2>
                            <p class="text-muted-foreground leading-relaxed mb-4">
                                You have certain rights regarding your personal data:
                            </p>
                            <ul class="list-disc list-inside space-y-2 text-muted-foreground">
                                <li>The right to access, update or delete the information we have on you</li>
                                <li>The right to rectification of inaccurate personal data</li>
                                <li>The right to object to processing of your personal data</li>
                                <li>The right to restrict processing of your personal data</li>
                                <li>The right to data portability</li>
                            </ul>
                        </section>

                        <!-- Section 9 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">9. Changes to Privacy Policy</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date at the top of this Privacy Policy.
                            </p>
                        </section>

                        <!-- Section 10 -->
                        <section>
                            <h2 class="text-2xl font-bold text-foreground mb-4">10. Contact Us</h2>
                            <p class="text-muted-foreground leading-relaxed">
                                If you have any questions about this Privacy Policy, please contact us at privacy@levelupacademy.com
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
