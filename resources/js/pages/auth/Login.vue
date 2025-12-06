<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useLogo } from '@/composables/useLogo';

const isDarkMode = ref(false);
const mouseX = ref(0);
const mouseY = ref(0);
const { hasLogo, logo, getLightLogo, getDarkLogo } = useLogo();

defineProps<{
    status?: string;
    canResetPassword: [REDACTED:password];
    canRegister: boolean;
}>();

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
    <Head title="Log in" />
    <div
        class="min-h-screen bg-background text-foreground transition-colors duration-500 overflow-hidden"
    >
        <!-- Animated Background Gradient Orbs -->
        <div class="fixed inset-0 -z-10 overflow-hidden">
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-5 bg-primary top-20 -left-40 animate-blob"
            />
            <div
                class="absolute w-80 h-80 rounded-full blur-3xl opacity-5 bg-accent bottom-20 -right-40 animate-blob"
                style="animation-delay: 2s"
            />
        </div>

        <!-- Header with Theme Toggle -->
        <header
            class="sticky top-0 z-50 backdrop-blur-md bg-background/50 border-b border-border transition-colors duration-500"
        >
            <nav class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex items-center gap-3 group cursor-pointer"
                >
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
            </nav>
        </header>

        <!-- Main Content -->
        <div class="flex min-h-[calc(100vh-80px)] flex-col items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-sm">
                <!-- Status Message -->
                <div
                    v-if="status"
                    class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-center text-sm font-medium text-green-700 dark:text-green-300"
                >
                    {{ status }}
                </div>

                <!-- Form Container -->
                <div
                    class="rounded-2xl bg-card border border-border p-8 shadow-xl"
                >
                    <!-- Header -->
                    <div class="mb-8 flex flex-col items-center gap-4">
                        <div class="space-y-2 text-center">
                            <h1
                                class="text-2xl lg:text-3xl font-bold text-primary"
                            >
                                Welcome Back
                            </h1>
                            <p
                                class="text-center text-sm text-muted-foreground"
                            >
                                Continue your learning journey at LevelUp Academy
                            </p>
                        </div>
                    </div>

                    <!-- Login Form -->
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="flex flex-col gap-6"
                    >
                        <!-- Email Field -->
                        <div class="grid gap-2">
                            <Label
                                for="email"
                                class="text-sm font-semibold text-foreground"
                            >
                                Email Address
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="you@example.com"
                                class="rounded-lg border border-border bg-background px-4 py-2.5 text-foreground placeholder-muted-foreground transition-all duration-300 focus:border-primary focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div class="grid gap-2">
                            <div class="flex items-center justify-between">
                                <Label
                                    for="password"
                                    class="text-sm font-semibold text-foreground"
                                >
                                    Password
                                </Label>
                                <TextLink
                                    v-if="canResetPassword"
                                    :href="request()"
                                    class="text-xs font-medium text-primary hover:text-primary/80 transition-colors"
                                    :tabindex="5"
                                >
                                    Forgot password?
                                </TextLink>
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="rounded-lg border border-border bg-background px-4 py-2.5 text-foreground placeholder-muted-foreground transition-all duration-300 focus:border-primary focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <Label
                                for="remember"
                                class="flex items-center space-x-2.5 cursor-pointer"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                />
                                <span class="text-sm font-medium text-foreground">
                                    Remember me
                                </span>
                            </Label>
                        </div>

                        <!-- Login Button -->
                        <Button
                            type="submit"
                            class="mt-4 w-full rounded-lg bg-primary text-primary-foreground py-2.5 px-4 font-semibold shadow-lg hover:bg-primary/90 transition-all duration-300"
                            :tabindex="4"
                            :disabled="processing"
                            data-test="login-button"
                        >
                            <Spinner v-if="processing" />
                            <span v-else>Log in to Your Account</span>
                        </Button>

                        <!-- Sign Up Link -->
                         <div
                             v-if="canRegister"
                             class="text-center text-sm text-muted-foreground"
                         >
                             Don't have an account?
                             <TextLink
                                 :href="register()"
                                 class="font-semibold text-primary hover:text-primary/80 transition-colors"
                                 :tabindex="5"
                             >
                                 Sign up for free
                             </TextLink>
                         </div>
                        </Form>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-8 text-center text-xs text-muted-foreground">
                        <p>
                         By logging in, you agree to our
                         <a
                             href="#"
                             class="text-primary hover:underline"
                         >
                             Terms of Service
                         </a>
                         and
                         <a
                             href="#"
                             class="text-primary hover:underline"
                         >
                             Privacy Policy
                         </a>
                        </p>
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
