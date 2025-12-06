<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { useLogo } from '@/composables/useLogo';

const mouseX = ref(0);
const mouseY = ref(0);
const isDarkMode = ref(false);
const showModal = ref(false);
const { hasLogo, logo, getLightLogo, getDarkLogo } = useLogo();

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    // Check for saved theme preference or system preference
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDarkMode.value = false;
        document.documentElement.classList.remove('dark');
    }

    document.addEventListener('mousemove', (e) => {
        mouseX.value = e.clientX;
        mouseY.value = e.clientY;
    });
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="min-h-screen bg-background text-foreground transition-colors duration-500 overflow-hidden"
    >
        <!-- Animated Background Gradient Orbs -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-20 dark:opacity-10 bg-blue-400 dark:bg-blue-600 top-20 -left-40 animate-blob"
            />
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-20 dark:opacity-10 bg-purple-400 dark:bg-purple-600 bottom-20 -right-40 animate-blob"
                style="animation-delay: 2s"
            />
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-20 dark:opacity-10 bg-pink-400 dark:bg-pink-600 top-1/2 left-1/2 animate-blob"
                style="animation-delay: 4s"
            />
        </div>

        <!-- Gradient Light Effect Following Mouse -->
        <div
            class="fixed inset-0 -z-10 pointer-events-none"
            :style="{
                background: `radial-gradient(600px at ${mouseX}px ${mouseY}px, rgba(59, 130, 246, 0.1), transparent 80%)`,
            }"
        />

        <!-- Header Navigation -->
        <header
            class="sticky top-0 z-50 backdrop-blur-md bg-white/50 dark:bg-gray-950/50 border-b border-gray-200/50 dark:border-gray-800/50 transition-colors duration-500"
        >
            <nav class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3 group cursor-pointer">
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
                    <span class="font-bold text-xl bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">
                        LevelUp Academy
                    </span>
                </div>

                <!-- Nav Links -->
                <div class="flex items-center gap-3">
                    <!-- Theme Toggle Button -->
                    <button
                        @click="toggleTheme"
                        class="p-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 border border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500"
                        :aria-label="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                    >
                        <svg
                            v-if="!isDarkMode"
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm5.657-9.193a1 1 0 00-1.414 0l-.707.707A1 1 0 005.05 6.464l.707-.707a1 1 0 011.414 0zm0 18.186a1 1 0 001.414 0l.707-.707a1 1 0 00-1.414-1.414l-.707.707a1 1 0 000 1.414zM5.05 6.464a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-300"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-300"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white hover:shadow-lg hover:shadow-blue-500/50 transition-all duration-300"
                        >
                            Get Started
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
            <!-- Hero Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="space-y-4">
                        <div class="inline-block">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50"
                            >
                                Welcome to the Future
                            </span>
                        </div>
                        <h1
                            class="text-5xl lg:text-6xl font-bold leading-tight space-y-2"
                        >
                            <span class="block">Level Up Your</span>
                            <span
                                class="block bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 bg-clip-text text-transparent"
                            >
                                Learning Journey
                            </span>
                        </h1>
                    </div>

                    <p
                        class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-2xl"
                    >
                        Unlock your potential with our gamified learning platform. Track your
                        progress, earn achievements, and compete on the leaderboard while
                        mastering new skills.
                    </p>

                    <!-- Feature Grid -->
                    <div class="grid grid-cols-2 gap-4 pt-4">
                        <div
                            v-for="(feature, index) in [
                                { icon: '🎯', title: 'Gamified', desc: 'Earn XP & Streaks' },
                                { icon: '📈', title: 'Track Progress', desc: 'Real-time Stats' },
                                { icon: '🏆', title: 'Compete', desc: 'Leaderboards' },
                                { icon: '⚡', title: 'Lightning Fast', desc: 'Smooth & Snappy' },
                            ]"
                            :key="index"
                            class="group p-4 rounded-xl bg-white/50 dark:bg-gray-800/30 border border-gray-200/50 dark:border-gray-700/50 hover:bg-white dark:hover:bg-gray-800/60 hover:border-blue-300 dark:hover:border-blue-700/50 transition-all duration-300 backdrop-blur-sm"
                        >
                            <div class="text-2xl mb-2">{{ feature.icon }}</div>
                            <h3
                                class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                            >
                                {{ feature.title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ feature.desc }}
                            </p>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="px-8 py-3 rounded-xl font-semibold bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white hover:shadow-2xl hover:shadow-blue-500/50 dark:hover:shadow-blue-500/30 transition-all duration-300 text-center transform hover:scale-105"
                        >
                            Start Your Journey
                        </Link>
                        <button
                            @click="showModal = true"
                            class="px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 text-center"
                        >
                            Learn More
                        </button>
                    </div>
                </div>

                <!-- Right Illustration -->
                <div class="relative h-96 lg:h-full lg:min-h-[500px]">
                    <!-- Floating Cards -->
                    <div class="absolute inset-0 perspective">
                        <!-- Card 1 -->
                        <div
                            class="absolute w-48 h-32 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6 text-white shadow-2xl top-0 right-0 animate-float"
                            style="animation-delay: 0s"
                        >
                            <div class="text-2xl mb-2">⚡</div>
                            <h3 class="font-bold text-sm mb-1">Instant Progress</h3>
                            <p class="text-xs opacity-90">Track your growth</p>
                        </div>

                        <!-- Card 2 -->
                        <div
                            class="absolute w-48 h-32 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 p-6 text-white shadow-2xl bottom-12 left-0 animate-float"
                            style="animation-delay: 1s"
                        >
                            <div class="text-2xl mb-2">🔥</div>
                            <h3 class="font-bold text-sm mb-1">Daily Streaks</h3>
                            <p class="text-xs opacity-90">Build consistency</p>
                        </div>

                        <!-- Card 3 -->
                        <div
                            class="absolute w-48 h-32 rounded-2xl bg-gradient-to-br from-pink-500 to-pink-600 dark:from-pink-600 dark:to-pink-700 p-6 text-white shadow-2xl bottom-0 right-12 animate-float"
                            style="animation-delay: 2s"
                        >
                            <div class="text-2xl mb-2">🏆</div>
                            <h3 class="font-bold text-sm mb-1">Earn Badges</h3>
                            <p class="text-xs opacity-90">Gain recognition</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Section -->
            <div
                class="grid grid-cols-3 gap-4 lg:gap-6 py-12 border-t border-gray-200 dark:border-gray-800"
            >
                <div
                    v-for="(stat, index) in [
                        { value: '10K+', label: 'Active Users' },
                        { value: '500+', label: 'Learning Resources' },
                        { value: '99.9%', label: 'Uptime' },
                    ]"
                    :key="index"
                    class="text-center group"
                >
                    <p
                        class="text-3xl lg:text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent group-hover:from-blue-700 group-hover:to-purple-700 dark:group-hover:from-blue-300 dark:group-hover:to-purple-300 transition-all duration-300"
                    >
                        {{ stat.value }}
                    </p>
                    <p
                        class="text-gray-600 dark:text-gray-400 text-sm mt-2 group-hover:text-gray-900 dark:group-hover:text-gray-300 transition-colors duration-300"
                    >
                        {{ stat.label }}
                    </p>
                </div>
            </div>

            <!-- Features Section -->
            <div class="mt-20">
                <div class="text-center mb-12 space-y-3">
                    <h2 class="text-3xl lg:text-4xl font-bold">Why Choose LevelUp Academy?</h2>
                    <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                        Everything you need to succeed in one powerful platform
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="(feature, index) in [
                            {
                                icon: '🎮',
                                title: 'Gamification',
                                desc: 'Earn points, unlock badges, and build streaks to stay motivated',
                            },
                            {
                                icon: '📊',
                                title: 'Advanced Analytics',
                                desc: 'Track progress with detailed insights and performance metrics',
                            },
                            {
                                icon: '🏅',
                                title: 'Leaderboards',
                                desc: 'Compete with peers and earn recognition for achievements',
                            },
                        ]"
                        :key="index"
                        class="group p-8 rounded-2xl bg-white/70 dark:bg-gray-800/40 border border-gray-200/50 dark:border-gray-700/50 hover:bg-white dark:hover:bg-gray-800/80 hover:shadow-xl dark:hover:shadow-2xl hover:border-blue-300 dark:hover:border-blue-600/50 backdrop-blur-sm transition-all duration-500 cursor-default"
                    >
                        <div
                            class="text-4xl mb-4 group-hover:scale-110 transition-transform duration-300 inline-block"
                        >
                            {{ feature.icon }}
                        </div>
                        <h3 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">
                            {{ feature.title }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ feature.desc }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom CTA -->
            <div
                class="mt-20 py-12 px-8 rounded-3xl bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white text-center"
            >
                <h2 class="text-3xl lg:text-4xl font-bold mb-4">Ready to Begin?</h2>
                <p class="text-lg opacity-90 mb-8 max-w-2xl mx-auto">
                    Join thousands of learners who are already transforming their skills
                </p>
                <Link
                    v-if="canRegister"
                    :href="register()"
                    class="inline-block px-8 py-3 rounded-xl font-semibold bg-white text-blue-600 hover:bg-gray-100 transition-all duration-300 transform hover:scale-105"
                >
                    Create Free Account
                </Link>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="mt-20 py-8 border-t border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-950/50 backdrop-blur-sm"
        >
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        © 2024 LevelUp Academy. All rights reserved.
                    </p>
                    <div class="flex gap-6">
                        <a
                            href="https://laravel.com"
                            target="_blank"
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            Docs
                        </a>
                        <a
                            href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            Privacy
                        </a>
                        <a
                            href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                        >
                            Contact
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-black/50"
                @click="showModal = false"
            />
            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 max-w-sm w-full">
                <button
                    @click="showModal = false"
                    class="absolute top-4 right-4 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">AYOKO NGANI</h2>
                    <p class="text-gray-600 dark:text-gray-400">Coming soon...</p>
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

@keyframes float {
    0%,
    100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) rotate(5deg);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.perspective {
    perspective: 1000px;
}
</style>
