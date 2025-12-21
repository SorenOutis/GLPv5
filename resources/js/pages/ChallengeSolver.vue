<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { defineAsyncComponent } from 'vue';
const CodeEditor = defineAsyncComponent(() => import('@/components/CodeEditor.vue'));
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import { Code2, Play, RotateCcw, Check, X, Zap, Maximize2, Minimize2 } from 'lucide-vue-next';

interface Challenge {
    id: number;
    title: string;
    difficulty: string;
    description: string;
    problem_statement: string;
    requirements: string[];
    example_input: string;
    example_output: string;
    tips: string[];
    xpReward: number;
    language: string;
    category: string;
}

interface Props {
    challenge: Challenge;
}

const props = defineProps<Props>();
const editorRef = ref<InstanceType<typeof CodeEditor> | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Coding',
        href: '/coding',
    },
    {
        title: props.challenge.title,
        href: '#',
    },
];

const isSubmitting = ref(false);
const output = ref('');
const isSuccess = ref(false);
const showOutput = ref(false);
const isMobile = ref(false);
const isEditorExpanded = ref(false);
const testCases = ref([
    { input: 'Test Input 1', expected: 'Expected Output 1', passed: false },
    { input: 'Test Input 2', expected: 'Expected Output 2', passed: false },
]);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024;
    // Default to expanded on mobile
    if (isMobile.value) {
        isEditorExpanded.value = true;
    }
};

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkMobile);
});

const getDifficultyColor = (difficulty: string) => {
    const colors: Record<string, string> = {
        'Beginner': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'Intermediate': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'Advanced': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return colors[difficulty] || 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200';
};

const getDifficultyIcon = (difficulty: string) => {
    switch (difficulty) {
        case 'Beginner':
            return '⚡';
        case 'Intermediate':
            return '🔥';
        case 'Advanced':
            return '💪';
        default:
            return '⭐';
    }
};

const runCode = async () => {
    if (!editorRef.value) return;

    isSubmitting.value = true;
    showOutput.value = true;
    const code = editorRef.value.getCode();

    try {
        const response = await fetch('/api/coding/run', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                code,
                language: props.challenge.language.toLowerCase(),
                challengeId: props.challenge.id,
            }),
        });

        const data = await response.json();
        output.value = data.output || data.error || 'No output';
        isSuccess.value = response.ok && !data.error;
    } catch (error) {
        output.value = `Error: ${error instanceof Error ? error.message : 'Unknown error'}`;
        isSuccess.value = false;
    } finally {
        isSubmitting.value = false;
    }
};

const submitChallenge = async () => {
    if (!editorRef.value) return;

    isSubmitting.value = true;
    const code = editorRef.value.getCode();

    try {
        const response = await fetch('/api/coding/submit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                code,
                language: props.challenge.language.toLowerCase(),
                challengeId: props.challenge.id,
            }),
        });

        const data = await response.json();
        output.value = data.message || 'Challenge submitted!';
        isSuccess.value = response.ok && data.passed;
        showOutput.value = true;
    } catch (error) {
        output.value = `Error: ${error instanceof Error ? error.message : 'Unknown error'}`;
        isSuccess.value = false;
        showOutput.value = true;
    } finally {
        isSubmitting.value = false;
    }
};

const resetCode = () => {
    if (editorRef.value) {
        editorRef.value.clearCode();
        showOutput.value = false;
        output.value = '';
    }
};
</script>

<template>
    <Head :title="`${challenge.title} - Code Challenge`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-auto rounded-xl p-4">
            <!-- Challenge Header -->
            <div class="space-y-2 mb-6">
                <div class="flex items-center gap-2">
                    <Code2 class="w-8 h-8 text-primary" />
                    <div>
                        <h1 class="text-3xl font-bold">{{ challenge.title }}</h1>
                        <p class="text-muted-foreground">{{ challenge.category }}</p>
                    </div>
                </div>
            </div>

            <!-- Challenge Info Cards -->
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-blue-50 to-blue-50/50 dark:from-blue-950/20 dark:to-blue-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Difficulty</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">{{ getDifficultyIcon(challenge.difficulty) }}</span>
                            <span class="font-semibold">{{ challenge.difficulty }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-purple-50 to-purple-50/50 dark:from-purple-950/20 dark:to-purple-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Language</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-lg font-semibold">{{ challenge.language }}</div>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-orange-50 to-orange-50/50 dark:from-orange-950/20 dark:to-orange-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium flex items-center gap-2">
                            <Zap class="w-4 h-4" />
                            XP Reward
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ challenge.xpReward }}</div>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border bg-gradient-to-br from-green-50 to-green-50/50 dark:from-green-950/20 dark:to-green-950/10">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-lg font-semibold text-yellow-600">In Progress</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Area -->
            <div class="grid gap-4 flex-1 min-h-0" :class="isEditorExpanded || isMobile ? 'grid-cols-1' : 'lg:grid-cols-2'">
                <!-- Left: Challenge Description & Instructions -->
                <Card v-if="!isEditorExpanded" class="border-sidebar-border/70 dark:border-sidebar-border flex flex-col overflow-hidden max-h-96 lg:max-h-none">
                    <CardHeader>
                        <CardTitle>Challenge Description</CardTitle>
                        <CardDescription>Read the requirements carefully</CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1 overflow-y-auto">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold mb-2">Problem Statement</h3>
                                <p class="text-sm text-muted-foreground whitespace-pre-wrap">{{ challenge.problem_statement }}</p>
                            </div>

                            <div v-if="challenge.requirements && challenge.requirements.length > 0">
                                <h3 class="font-semibold mb-2">Requirements</h3>
                                <ul class="text-sm text-muted-foreground space-y-1 list-disc list-inside">
                                    <li v-for="(req, index) in challenge.requirements" :key="index">
                                        {{ typeof req === 'string' ? req : req.requirement || req }}
                                    </li>
                                </ul>
                            </div>

                            <div v-if="challenge.example_input && challenge.example_output">
                                <h3 class="font-semibold mb-2">Example</h3>
                                <div class="bg-slate-100 dark:bg-slate-800 p-3 rounded text-xs font-mono text-muted-foreground whitespace-pre-wrap break-words">
                                    <div>Input: {{ challenge.example_input }}</div>
                                    <div>Output: {{ challenge.example_output }}</div>
                                </div>
                            </div>

                            <div v-if="challenge.tips && challenge.tips.length > 0">
                                <h3 class="font-semibold mb-2">Tips</h3>
                                <ul class="text-sm text-muted-foreground space-y-1 list-disc list-inside">
                                    <li v-for="(tip, index) in challenge.tips" :key="index">
                                        {{ typeof tip === 'string' ? tip : tip.tip || tip }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Right: Code Editor & Output -->
                <div class="flex flex-col gap-4 min-h-0">
                    <!-- Code Editor -->
                    <Card class="border-sidebar-border/70 dark:border-sidebar-border flex-1 flex flex-col overflow-hidden">
                        <CardHeader class="pb-3 flex items-center justify-between">
                            <CardTitle class="text-base">Code Editor</CardTitle>
                            <button
                                v-if="!isMobile"
                                @click="isEditorExpanded = !isEditorExpanded"
                                class="p-1 hover:bg-sidebar-border/20 rounded-md transition-colors"
                                :title="isEditorExpanded ? 'Collapse' : 'Expand'"
                            >
                                <component
                                    :is="isEditorExpanded ? Minimize2 : Maximize2"
                                    class="w-5 h-5"
                                />
                            </button>
                        </CardHeader>
                        <CardContent class="flex-1 overflow-hidden p-0 px-4">
                            <CodeEditor
                                ref="editorRef"
                                :language="(challenge.language.toLowerCase() as 'python' | 'java' | 'javascript')"
                                :height="isMobile ? 'auto' : '100%'"
                                :min-height="isMobile ? '500px' : '300px'"
                            />
                        </CardContent>
                    </Card>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 md:gap-3" :class="isMobile ? 'flex-col' : 'flex-row'">
                        <Button
                            @click="runCode"
                            :disabled="isSubmitting"
                            class="flex items-center gap-2 flex-1"
                            :size="isMobile ? 'lg' : 'default'"
                        >
                            <Play class="w-4 h-4" />
                            {{ isSubmitting ? 'Running...' : 'Run Code' }}
                        </Button>

                        <Button
                            @click="submitChallenge"
                            :disabled="isSubmitting"
                            variant="default"
                            class="flex items-center gap-2 flex-1"
                            :size="isMobile ? 'lg' : 'default'"
                        >
                            <Check class="w-4 h-4" />
                            {{ isSubmitting ? 'Submitting...' : 'Submit' }}
                        </Button>

                        <Button
                            @click="resetCode"
                            :disabled="isSubmitting"
                            variant="outline"
                            class="flex items-center gap-2"
                            :size="isMobile ? 'lg' : 'default'"
                        >
                            <RotateCcw class="w-4 h-4" />
                            Reset
                        </Button>
                    </div>

                    <!-- Output Section -->
                    <Card
                        v-if="showOutput"
                        class="border-sidebar-border/70 dark:border-sidebar-border"
                        :class="{
                            'border-green-500/30 bg-green-50 dark:bg-green-950/20': isSuccess,
                            'border-red-500/30 bg-red-50 dark:bg-red-950/20': !isSuccess,
                        }"
                    >
                        <CardHeader class="pb-2">
                            <div class="flex items-center gap-2">
                                <component
                                    :is="isSuccess ? Check : X"
                                    :class="[
                                        'w-4 h-4',
                                        isSuccess ? 'text-green-600' : 'text-red-600',
                                    ]"
                                />
                                <CardTitle class="text-sm">
                                    {{ isSuccess ? 'Success' : 'Error' }}
                                </CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="bg-slate-100 dark:bg-slate-800 p-3 rounded text-xs font-mono whitespace-pre-wrap break-words max-h-40 overflow-y-auto">
                                {{ output }}
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.cm-editor) {
    height: 100%;
}
</style>
