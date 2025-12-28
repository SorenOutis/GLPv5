<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Select from '@/components/ui/select/Select.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { ArrowLeft } from 'lucide-vue-next';

interface Game {
    id: number;
    name: string;
    description: string;
    category: string;
    difficulty: 'Beginner' | 'Intermediate' | 'Advanced';
    thumbnail: string;
    badge: string;
    is_active: boolean;
}

const props = defineProps<{
    game: Game;
}>();

const form = useForm({
    name: props.game.name,
    description: props.game.description,
    category: props.game.category,
    difficulty: props.game.difficulty as const,
    thumbnail: props.game.thumbnail,
    badge: props.game.badge,
    is_active: props.game.is_active,
});

const submit = () => {
    form.put(route('admin.games.update', props.game.id));
};
</script>

<template>
    <Head :title="`Edit ${props.game.name}`" />
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <router-link :href="route('admin.games.index')">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </router-link>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Edit Game
                    </h1>
                    <p class="mt-1 text-slate-600 dark:text-slate-400">
                        Update the game information
                    </p>
                </div>
            </div>

            <!-- Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>{{ props.game.name }}</CardTitle>
                    <CardDescription>Modify the game details below</CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Game Name *
                            </label>
                            <Input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., Code Quest"
                                :error="form.errors.name"
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Description *
                            </label>
                            <Textarea
                                v-model="form.description"
                                placeholder="Describe the game and what players will learn..."
                                class="h-24"
                            />
                            <p v-if="form.errors.description" class="text-xs text-red-600 dark:text-red-400">
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <!-- Category & Difficulty -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Category *
                                </label>
                                <Input
                                    v-model="form.category"
                                    type="text"
                                    placeholder="e.g., Coding"
                                    :error="form.errors.category"
                                />
                                <p v-if="form.errors.category" class="text-xs text-red-600 dark:text-red-400">
                                    {{ form.errors.category }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Difficulty *
                                </label>
                                <Select v-model="form.difficulty" :error="form.errors.difficulty">
                                    <option value="Beginner">Beginner</option>
                                    <option value="Intermediate">Intermediate</option>
                                    <option value="Advanced">Advanced</option>
                                </Select>
                                <p v-if="form.errors.difficulty" class="text-xs text-red-600 dark:text-red-400">
                                    {{ form.errors.difficulty }}
                                </p>
                            </div>
                        </div>

                        <!-- Thumbnail & Badge -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Thumbnail URL *
                                </label>
                                <Input
                                    v-model="form.thumbnail"
                                    type="url"
                                    placeholder="https://example.com/image.jpg"
                                    :error="form.errors.thumbnail"
                                />
                                <p v-if="form.errors.thumbnail" class="text-xs text-red-600 dark:text-red-400">
                                    {{ form.errors.thumbnail }}
                                </p>
                                <div v-if="form.thumbnail" class="mt-2">
                                    <img :src="form.thumbnail" alt="Preview" class="h-24 w-full object-cover rounded" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Badge (Emoji) *
                                </label>
                                <Input
                                    v-model="form.badge"
                                    type="text"
                                    placeholder="e.g., 🎮"
                                    maxlength="10"
                                    :error="form.errors.badge"
                                />
                                <p v-if="form.errors.badge" class="text-xs text-red-600 dark:text-red-400">
                                    {{ form.errors.badge }}
                                </p>
                                <div v-if="form.badge" class="text-4xl mt-2">
                                    {{ form.badge }}
                                </div>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/30">
                            <Checkbox v-model:checked="form.is_active" />
                            <div>
                                <p class="text-sm font-medium text-slate-900 dark:text-white">
                                    Active
                                </p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">
                                    Make this game visible to users
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4 pt-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </Button>
                            <router-link :href="route('admin.games.index')">
                                <Button variant="outline">
                                    Cancel
                                </Button>
                            </router-link>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template>
