<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Input from '@/components/ui/input/Input.vue';
import Select from '@/components/ui/select/Select.vue';
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue';
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue';
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue';
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue';
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue';
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue';
import { Pencil, Trash2, Plus, Eye, EyeOff } from 'lucide-vue-next';

interface Game {
    id: number;
    name: string;
    description: string;
    category: string;
    difficulty: 'Beginner' | 'Intermediate' | 'Advanced';
    thumbnail: string;
    badge: string;
    is_active: boolean;
    created_at: string;
}

interface PaginatedResponse {
    data: Game[];
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
}

const props = defineProps<{
    games: PaginatedResponse;
    filters: {
        search?: string;
        difficulty?: string;
    };
}>();

const page = usePage();
const searchQuery = ref(props.filters.search || '');
const difficultyFilter = ref(props.filters.difficulty || '');
const deleteGameId = ref<number | null>(null);
const showDeleteDialog = ref(false);

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Beginner': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Intermediate': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Advanced': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800';
};

const handleSearch = () => {
    router.get(route('admin.games.index'), {
        search: searchQuery.value || undefined,
        difficulty: difficultyFilter.value || undefined,
    });
};

const handleFilter = () => {
    router.get(route('admin.games.index'), {
        search: searchQuery.value || undefined,
        difficulty: difficultyFilter.value || undefined,
    });
};

const confirmDelete = (gameId: number) => {
    deleteGameId.value = gameId;
    showDeleteDialog.value = true;
};

const deleteGame = () => {
    if (deleteGameId.value) {
        router.delete(route('admin.games.destroy', deleteGameId.value), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                deleteGameId.value = null;
            },
        });
    }
};

const toggleGameStatus = (game: Game) => {
    router.post(route('admin.games.toggle', game.id));
};

const goToPage = (page: number) => {
    router.get(route('admin.games.index'), {
        page,
        search: searchQuery.value || undefined,
        difficulty: difficultyFilter.value || undefined,
    });
};
</script>

<template>
    <Head title="Games Management" />
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        Games Management
                    </h1>
                    <p class="mt-1 text-slate-600 dark:text-slate-400">
                        Manage all games in the platform
                    </p>
                </div>
                <router-link :href="route('admin.games.create')">
                    <Button class="gap-2">
                        <Plus class="h-4 w-4" />
                        Add Game
                    </Button>
                </router-link>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Filters</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Search
                            </label>
                            <Input
                                v-model="searchQuery"
                                placeholder="Search by name or description..."
                                @keyup.enter="handleSearch"
                            />
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                Difficulty
                            </label>
                            <Select v-model="difficultyFilter" @update:model-value="handleFilter">
                                <option value="">All difficulties</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </Select>
                        </div>

                        <div class="flex items-end">
                            <Button @click="handleSearch" variant="outline" class="w-full">
                                Search
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Games Table -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">
                        {{ props.games.total }} Games
                    </CardTitle>
                    <CardDescription>
                        Page {{ props.games.current_page }} of {{ props.games.last_page }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 dark:border-slate-700">
                                    <th class="px-4 py-3 text-left font-semibold text-slate-900 dark:text-white">
                                        Name
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-900 dark:text-white">
                                        Category
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-900 dark:text-white">
                                        Difficulty
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-900 dark:text-white">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-slate-900 dark:text-white">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="game in props.games.data"
                                    :key="game.id"
                                    class="border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/50"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900 dark:text-white">
                                            {{ game.name }}
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">
                                            {{ game.description }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                                        {{ game.category }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            :class="['px-2 py-1 rounded-full text-xs font-semibold', getDifficultyColor(game.difficulty)]"
                                        >
                                            {{ game.difficulty }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            @click="toggleGameStatus(game)"
                                            :class="[
                                                'inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs font-semibold',
                                                game.is_active
                                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                                            ]"
                                        >
                                            <component
                                                :is="game.is_active ? Eye : EyeOff"
                                                class="h-3 w-3"
                                            />
                                            {{ game.is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <router-link :href="route('admin.games.edit', game.id)">
                                                <Button size="sm" variant="outline" class="gap-2">
                                                    <Pencil class="h-4 w-4" />
                                                    Edit
                                                </Button>
                                            </router-link>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                class="gap-2 text-red-600 hover:text-red-700 dark:text-red-400"
                                                @click="confirmDelete(game.id)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                Delete
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Empty State -->
                        <div
                            v-if="props.games.data.length === 0"
                            class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-900/30"
                        >
                            <p class="text-slate-600 dark:text-slate-400">
                                No games found. Create one to get started.
                            </p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="props.games.last_page > 1" class="mt-6 flex items-center justify-between">
                        <Button
                            @click="goToPage(props.games.current_page - 1)"
                            :disabled="props.games.current_page === 1"
                            variant="outline"
                        >
                            Previous
                        </Button>

                        <div class="text-sm text-slate-600 dark:text-slate-400">
                            Page {{ props.games.current_page }} of {{ props.games.last_page }}
                        </div>

                        <Button
                            @click="goToPage(props.games.current_page + 1)"
                            :disabled="props.games.current_page === props.games.last_page"
                            variant="outline"
                        >
                            Next
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <AlertDialogContent>
                <AlertDialogTitle>Delete Game</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure you want to delete this game? This action cannot be undone.
                </AlertDialogDescription>
                <div class="flex gap-4 justify-end">
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction @click="deleteGame" class="bg-red-600 hover:bg-red-700">
                        Delete
                    </AlertDialogAction>
                </div>
            </AlertDialogContent>
        </AlertDialog>
    </AdminLayout>
</template>
