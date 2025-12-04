<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface FormData {
    name: string;
    email: string;
    cover_photo?: File | null;
}

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;

// Cover photo state
const coverPhotoPreview = ref<string | null>(null);
const coverPhotoFile = ref<File | null>(null);
const coverPhotoInput = ref<HTMLInputElement | null>(null);

// Game-style reactive state
const profileHovered = ref(false);
const showConfetti = ref(false);
const particles = ref<Array<{ id: number; x: number; y: number }>>([]);
const particleCounter = ref(0);
const levelProgress = ref(0);
const profileLevel = computed(() => Math.floor((user.xp || 0) / 1000) + 1);
const nextLevelXp = computed(() => profileLevel.value * 1000);
const currentLevelXp = computed(() => (profileLevel.value - 1) * 1000);
const xpProgress = computed(() => {
    const current = (user.xp || 0) - currentLevelXp.value;
    const needed = nextLevelXp.value - currentLevelXp.value;
    return (current / needed) * 100;
});

// Animated stats
const stats = ref([
    { label: 'Achievements', value: 0, target: user.achievements_count || 0, icon: '🏆' },
    { label: 'Streaks', value: 0, target: user.streaks_count || 0, icon: '🔥' },
    { label: 'Level', value: 0, target: profileLevel.value, icon: '⭐' },
]);

onMounted(() => {
    // Animate stat counters
    stats.value.forEach((stat) => {
        let current = 0;
        const increment = Math.ceil(stat.target / 30);
        const interval = setInterval(() => {
            current += increment;
            if (current >= stat.target) {
                stat.value = stat.target;
                clearInterval(interval);
            } else {
                stat.value = current;
            }
        }, 30);
    });

    // Animate XP progress bar
    const xpInterval = setInterval(() => {
        if (levelProgress.value < xpProgress.value) {
            levelProgress.value = Math.min(
                levelProgress.value + 2,
                xpProgress.value
            );
        }
    }, 50);
    return () => clearInterval(xpInterval);
});

const createParticle = (event: MouseEvent) => {
    const rect = (event.currentTarget as HTMLElement).getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    const particle = {
        id: particleCounter.value++,
        x,
        y,
    };

    particles.value.push(particle);

    setTimeout(() => {
        particles.value = particles.value.filter((p) => p.id !== particle.id);
    }, 600);
};

const triggerConfetti = () => {
    showConfetti.value = true;
    setTimeout(() => {
        showConfetti.value = false;
    }, 2000);
};

const getGamerTag = () => {
    const tags = ['🎮', '⚔️', '🛡️', '💎', '👑', '🌟'];
    return tags[user.id % tags.length];
};

const handleCoverPhotoSelect = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (file) {
        coverPhotoFile.value = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            coverPhotoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const openCoverPhotoUpload = () => {
    coverPhotoInput.value?.click();
};

const removeCoverPhoto = () => {
    coverPhotoFile.value = null;
    coverPhotoPreview.value = null;
    if (coverPhotoInput.value) {
        coverPhotoInput.value.value = '';
    }
};

const getCoverPhotoUrl = () => {
    if (coverPhotoPreview.value) {
        return coverPhotoPreview.value;
    }
    if (user.cover_photo_path) {
        return `/storage/${user.cover_photo_path}`;
    }
    return null;
};

const onFormSubmit = (form: any) => {
    if (coverPhotoFile.value) {
        form.cover_photo = coverPhotoFile.value;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="space-y-8">
                <!-- Game Profile Card -->
                <div class="relative overflow-hidden rounded-lg border-2 border-purple-500 shadow-2xl transition-all duration-300 hover:border-purple-400 hover:shadow-lg"
                    style="border-color: rgba(168, 85, 247, 0.5); box-shadow: 0 0 20px rgba(168, 85, 247, 0.1);"
                    @mouseenter="profileHovered = true" @mouseleave="profileHovered = false">
                    <!-- Cover Photo Section -->
                    <div v-if="getCoverPhotoUrl()" class="relative h-48 overflow-hidden">
                        <img :src="getCoverPhotoUrl()" alt="Cover Photo" class="w-full h-full object-cover" />
                        <button @click="openCoverPhotoUpload"
                            class="absolute top-3 right-3 bg-black/50 hover:bg-black/70 text-white px-3 py-2 rounded-lg text-sm transition-colors">
                            📸 Change Cover
                        </button>
                    </div>
                    <div v-else class="relative h-48 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
                        <button @click="openCoverPhotoUpload"
                            class="absolute inset-0 w-full h-full flex items-center justify-center group">
                            <div class="text-center transition-transform group-hover:scale-110">
                                <div class="text-4xl mb-2">📸</div>
                                <div class="text-purple-300 group-hover:text-purple-200 font-medium">Add Cover Photo
                                </div>
                                <div class="text-xs text-purple-300/60 mt-1">Click to upload</div>
                            </div>
                        </button>
                    </div>

                    <!-- Content Section -->
                    <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8"
                        style="background-color: rgba(15, 23, 42, 0.8);">
                        <!-- Animated background -->
                        <div class="absolute inset-0" style="opacity: 0.2;">
                            <div class="absolute -right-1/2 -top-1/2 h-full w-full rounded-full bg-gradient-to-br from-purple-600 to-transparent blur-3xl transition-transform duration-500"
                                :class="{ 'scale-110': profileHovered }" />
                        </div>

                        <!-- Particle effects -->
                        <div v-for="particle in particles" :key="particle.id" class="absolute pointer-events-none">
                            <div class="text-2xl animate-bounce" :style="{
                                left: particle.x + 'px',
                                top: particle.y + 'px',
                                animation: `float-up 0.6s ease-out forwards`,
                            }">
                                ✨
                            </div>
                        </div>

                        <div class="relative z-10 space-y-6">
                            <!-- Header with Gamertag -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="mb-2 flex items-center gap-2">
                                        <span class="text-4xl">{{ getGamerTag() }}</span>
                                        <h1
                                            class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-purple-400">
                                            {{ user.name }}
                                        </h1>
                                    </div>
                                    <p class="text-sm text-purple-300/70">
                                        Level {{ profileLevel }} Player
                                    </p>
                                </div>
                            </div>

                            <!-- XP Progress Bar -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-purple-300">Experience Points</span>
                                    <span class="text-sm text-purple-400">{{ user.xp || 0 }} / {{ nextLevelXp }}
                                        XP</span>
                                </div>
                                <div class="relative h-4 w-full overflow-hidden rounded-full bg-slate-700 border border-purple-500"
                                    style="border-color: rgba(168, 85, 247, 0.3);">
                                    <div class="h-full bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 transition-all duration-300 rounded-full shadow-lg"
                                        :style="{ width: levelProgress + '%', boxShadow: '0 0 10px rgba(168, 85, 247, 0.5)' }" />
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent animate-pulse"
                                        style="opacity: 0.3;" :style="{ width: levelProgress + '%', opacity: 0.3 }" />
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-4">
                                <button v-for="(stat, index) in stats" :key="index" @click="createParticle"
                                    class="group relative overflow-hidden rounded-lg bg-slate-700 border border-purple-500 p-4 text-center transition-all duration-300 hover:border-purple-400 cursor-pointer hover:scale-105"
                                    style="background-color: rgba(51, 65, 85, 0.5); border-color: rgba(168, 85, 247, 0.3);">
                                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 transition-opacity duration-300 group-hover:opacity-20"
                                        style="opacity: 0;" />
                                    <div class="relative z-10">
                                        <div class="mb-2 text-3xl">{{ stat.icon }}</div>
                                        <div class="text-2xl font-bold text-purple-300">{{ stat.value }}</div>
                                        <div class="text-xs text-purple-300/60 mt-1">{{ stat.label }}</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="space-y-6">
                        <HeadingSmall title="Profile Information" description="Update your name and email address" />

                        <Form v-bind="ProfileController.update.form()" class="space-y-6"
                            v-slot="{ form, errors, processing, recentlySuccessful }" multipart
                            @submit="onFormSubmit(form)">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="name" class="flex items-center gap-2">
                                        <span>👤 Name</span>
                                    </Label>
                                    <Input id="name"
                                        class="mt-1 block w-full border-purple-500 focus:border-purple-500 focus:ring-purple-500"
                                        style="border-color: rgba(168, 85, 247, 0.2);" name="name"
                                        :default-value="user.name" required autocomplete="name"
                                        placeholder="Full name" />
                                    <InputError class="mt-2" :message="errors.name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="email" class="flex items-center gap-2">
                                        <span>📧 Email address</span>
                                    </Label>
                                    <Input id="email" type="email"
                                        class="mt-1 block w-full border-purple-500 focus:border-purple-500 focus:ring-purple-500"
                                        style="border-color: rgba(168, 85, 247, 0.2);" name="email"
                                        :default-value="user.email" required autocomplete="username"
                                        placeholder="Email address" />
                                    <InputError class="mt-2" :message="errors.email" />
                                </div>
                            </div>

                            <!-- Cover Photo Upload -->
                            <div class="grid gap-2">
                                <Label for="cover-photo" class="flex items-center gap-2">
                                    <span>📸 Cover Photo</span>
                                </Label>
                                <input ref="coverPhotoInput" type="file" id="cover-photo" name="cover_photo"
                                    accept="image/*" class="hidden" @change="handleCoverPhotoSelect" />
                                <div @click="openCoverPhotoUpload"
                                    class="relative border-2 border-dashed border-purple-500 rounded-lg p-6 cursor-pointer transition-all hover:border-purple-400 hover:bg-purple-500/5"
                                    style="border-color: rgba(168, 85, 247, 0.3);">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-purple-300">
                                                {{ coverPhotoFile ? coverPhotoFile.name : 'Click to select a cover photo' }}
                                            </p>
                                            <p class="text-xs text-purple-300/60 mt-1">
                                                PNG, JPG, GIF or WebP up to 5MB
                                            </p>
                                        </div>
                                        <div class="text-2xl">📁</div>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="errors.cover_photo" />
                                <div v-if="coverPhotoFile" class="flex items-center gap-2 mt-2">
                                    <button type="button" @click="removeCoverPhoto"
                                        class="text-xs text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                        ✕ Remove photo
                                    </button>
                                </div>
                            </div>

                            <div v-if="mustVerifyEmail && !user.email_verified_at">
                                <div class="rounded-lg bg-yellow-500 border border-yellow-500 p-4"
                                    style="background-color: rgba(234, 179, 8, 0.1); border-color: rgba(234, 179, 8, 0.3);">
                                    <p class="text-sm text-yellow-200">
                                        ⚠️ Your email address is unverified.
                                        <Link :href="send()" as="button"
                                            class="font-semibold text-yellow-300 underline decoration-yellow-400 underline-offset-2 hover:text-yellow-100 transition-colors">
                                        Click here to resend the verification email.
                                        </Link>
                                    </p>

                                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                        <div v-if="status === 'verification-link-sent'"
                                            class="mt-3 rounded bg-green-500 border border-green-500 p-3 text-sm font-medium text-green-300"
                                            style="background-color: rgba(34, 197, 94, 0.2); border-color: rgba(34, 197, 94, 0.3);">
                                            ✅ A new verification link has been sent to your email address.
                                        </div>
                                    </Transition>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <Button :disabled="processing" @click="triggerConfetti"
                                    data-test="update-profile-button"
                                    class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg"
                                    style="box-shadow: 0 10px 15px rgba(168, 85, 247, 0.3);">
                                    <span v-if="!processing">💾 Save Changes</span>
                                    <span v-else>⏳ Saving...</span>
                                </Button>

                                <Transition enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0 scale-95" leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0 scale-95">
                                    <div v-show="recentlySuccessful"
                                        class="flex items-center gap-2 text-sm font-medium text-green-400">
                                        ✅ Saved successfully!
                                    </div>
                                </Transition>
                            </div>
                        </Form>
                    </div>

                    <!-- Delete Account Section -->
                    <div class="border-t border-purple-500/20 pt-8">
                        <DeleteUser />
                    </div>
                </div>
        </SettingsLayout>
    </AppLayout>
</template>

<style scoped>
@keyframes float-up {
    0% {
        opacity: 1;
        transform: translateY(0) translateX(0);
    }

    50% {
        opacity: 1;
    }

    100% {
        opacity: 0;
        transform: translateY(-60px) translateX(20px) scale(0);
    }
}

:deep(input) {
    background-color: rgba(51, 65, 85, 0.5);
    color: rgb(226, 232, 240);
}

:deep(input::placeholder) {
    color: rgb(148, 163, 184);
}
</style>
