<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { community } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import AlertSuccess from '@/components/AlertSuccess.vue';

interface Post {
    id: number;
    title: string;
    content: string;
    excerpt: string;
    views: number;
    likes: number;
    is_pinned: boolean;
    is_approved: boolean;
    user: {
        id: number;
        name: string;
        profile_photo_path?: string;
    };
    reactions?: {
        id: number;
        reaction_type: string;
        user_id: number;
    }[];
    created_at: string;
}

interface Props {
    posts: {
        data: Post[];
        links: any;
    };
    stats: {
        totalPosts: number;
        activeCommunity: number;
        topContributor: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const currentUserId = page.props.auth.user?.id || 0;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Community',
        href: '#',
    },
];

const showNewPostModal = ref(false);
const selectedPost = ref<Post | null>(null);
const searchQuery = ref('');
const showSuccessAlert = ref(false);
const successMessage = ref('');

const form = useForm({
    title: '',
    content: '',
});

const filteredPosts = computed(() => {
    if (!searchQuery.value) return props.posts.data;
    const query = searchQuery.value.toLowerCase();
    return props.posts.data.filter(post =>
        post.title.toLowerCase().includes(query) ||
        post.excerpt.toLowerCase().includes(query)
    );
});

const handleSubmitPost = () => {
    form.post(community.form().action, {
        onSuccess: () => {
            form.reset();
            showNewPostModal.value = false;
            successMessage.value = 'Your post has been submitted successfully! It will appear once approved by moderators.';
            showSuccessAlert.value = true;
            // Auto-hide alert after 5 seconds
            setTimeout(() => {
                showSuccessAlert.value = false;
            }, 5000);
        },
    });
};

const handleReact = (post: Post, reactionType: string) => {
    router.post(`/community/${post.id}/react`, {
        reaction_type: reactionType,
    });
};

const getReactionEmoji = (type: string) => {
    const emojis: Record<string, string> = {
        'like': '👍',
        'love': '❤️',
        'haha': '😂',
        'wow': '😮',
        'sad': '😢',
        'angry': '😠',
    };
    return emojis[type] || '👍';
};

const getReactionCounts = (post: Post) => {
    if (!post.reactions) return {};
    return post.reactions.reduce((acc, reaction) => {
        acc[reaction.reaction_type] = (acc[reaction.reaction_type] || 0) + 1;
        return acc;
    }, {} as Record<string, number>);
};

const getUserReaction = (post: Post, userId: number) => {
    if (!post.reactions) return null;
    return post.reactions.find(r => r.user_id === userId)?.reaction_type || null;
};

const getTimeAgo = (date: string) => {
    const now = new Date();
    const postDate = new Date(date);
    const seconds = Math.floor((now.getTime() - postDate.getTime()) / 1000);
    
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;
    return postDate.toLocaleDateString();
};
</script>

<template>
    <Head title="Community" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <!-- Success Alert -->
            <div v-if="showSuccessAlert" class="fixed top-4 right-4 z-40 w-96">
                <AlertSuccess 
                    title="Post Submitted!"
                    :message="successMessage"
                />
            </div>

            <!-- Stats Section -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Total Posts</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-accent">{{ stats.totalPosts }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Community posts</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Today's Activity</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.activeCommunity }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Posts today</p>
                    </CardContent>
                </Card>

                <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium">Top Contributor</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-sm font-bold truncate">{{ stats.topContributor }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Most active</p>
                    </CardContent>
                </Card>
            </div>

            <!-- New Post Button & Search -->
            <div class="flex gap-4 items-center">
                <Button @click="showNewPostModal = true" class="gap-2">
                    <span>✎</span> New Post
                </Button>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search posts..."
                    class="flex-1 px-4 py-2 rounded-lg border border-sidebar-border/30 bg-background text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-accent"
                />
            </div>

            <!-- Posts List -->
            <div class="space-y-3">
                <div v-for="post in filteredPosts" :key="post.id"
                    class="p-4 rounded-lg border border-sidebar-border/30 hover:shadow-md transition-all duration-150 bg-background cursor-pointer group"
                    @click="selectedPost = post">
                    
                    <!-- Pinned Badge -->
                    <div v-if="post.is_pinned" class="inline-block mb-2">
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full font-medium">📌 Pinned</span>
                    </div>

                    <div class="flex items-start gap-4">
                        <!-- User Avatar -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-accent/20 border border-accent/30 flex items-center justify-center text-lg">
                                {{ post.user.name.charAt(0).toUpperCase() }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-sm">{{ post.user.name }}</h4>
                                        <span class="text-xs text-muted-foreground">{{ getTimeAgo(post.created_at) }}</span>
                                    </div>
                                    <h3 class="text-base font-bold truncate group-hover:text-accent transition-colors">{{ post.title }}</h3>
                                </div>
                            </div>
                            <p class="text-sm text-muted-foreground truncate mb-3">{{ post.excerpt }}</p>

                            <!-- Engagement Stats -->
                            <div class="flex gap-2 items-center text-xs text-muted-foreground">
                                <span>👁️ {{ post.reactions?.length || 0 }}</span>
                                <div class="flex gap-1">
                                    <span v-for="(count, type) in getReactionCounts(post)" :key="type" class="inline-flex items-center gap-1">
                                        {{ getReactionEmoji(type) }} {{ count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredPosts.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground mb-4">
                        {{ searchQuery ? 'No posts found matching your search' : 'No posts yet. Be the first to share!' }}
                    </p>
                    <Button v-if="!searchQuery" @click="showNewPostModal = true" variant="outline">Create First Post</Button>
                </div>
            </div>

            <!-- New Post Modal -->
            <div v-if="showNewPostModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border w-full max-w-2xl max-h-[90vh] overflow-auto">
                    <CardHeader class="sticky top-0 bg-background border-b border-sidebar-border/30">
                        <div class="flex items-center justify-between">
                            <CardTitle>Create New Post</CardTitle>
                            <button @click="showNewPostModal = false" class="text-muted-foreground hover:text-foreground">
                                ✕
                            </button>
                        </div>
                    </CardHeader>

                    <CardContent class="pt-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Title</label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="What's on your mind?"
                                class="w-full px-4 py-2 rounded-lg border border-sidebar-border/30 bg-background text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-accent"
                            />
                            <span v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Content</label>
                            <textarea
                                v-model="form.content"
                                placeholder="Share your thoughts, questions, or experiences with the community..."
                                rows="8"
                                class="w-full px-4 py-2 rounded-lg border border-sidebar-border/30 bg-background text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-accent resize-none"
                            ></textarea>
                            <span v-if="form.errors.content" class="text-xs text-red-500 mt-1">{{ form.errors.content }}</span>
                        </div>

                        <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg text-sm text-blue-800 dark:text-blue-200">
                            📝 Your post will be reviewed by moderators before appearing in the community.
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-sidebar-border/30">
                            <Button @click="handleSubmitPost" :disabled="form.processing">
                                {{ form.processing ? 'Submitting...' : 'Submit Post' }}
                            </Button>
                            <Button @click="showNewPostModal = false" variant="outline">Cancel</Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Post Detail Modal -->
            <div v-if="selectedPost" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
                <Card class="border-sidebar-border/70 dark:border-sidebar-border w-full max-w-2xl max-h-[90vh] overflow-auto">
                    <CardHeader class="sticky top-0 bg-background border-b border-sidebar-border/30">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-accent/20 border border-accent/30 flex items-center justify-center">
                                    {{ selectedPost.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <h3 class="font-semibold">{{ selectedPost.user.name }}</h3>
                                    <p class="text-xs text-muted-foreground">{{ getTimeAgo(selectedPost.created_at) }}</p>
                                </div>
                            </div>
                            <button @click="selectedPost = null" class="text-muted-foreground hover:text-foreground">
                                ✕
                            </button>
                        </div>
                        <CardTitle>{{ selectedPost.title }}</CardTitle>
                    </CardHeader>

                    <CardContent class="pt-6 space-y-4">
                        <div class="prose dark:prose-invert prose-sm max-w-none whitespace-pre-wrap">
                            {{ selectedPost.content }}
                        </div>

                        <div class="flex gap-4 py-4 border-y border-sidebar-border/30 text-sm text-muted-foreground">
                            <div class="flex gap-2">
                                <span v-for="(count, type) in getReactionCounts(selectedPost)" :key="type" class="inline-flex items-center gap-1">
                                    {{ getReactionEmoji(type) }} {{ count }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4 flex-wrap">
                            <button 
                                v-for="reactionType in ['like', 'love', 'haha', 'wow', 'sad', 'angry']"
                                :key="reactionType"
                                @click.stop="handleReact(selectedPost, reactionType)"
                                class="relative group px-3 py-2 rounded-lg border border-sidebar-border/30 transition-all duration-200 text-sm font-medium overflow-visible min-w-fit"
                                :class="{ 'bg-accent/20 border-accent shadow-lg': getUserReaction(selectedPost, currentUserId) === reactionType, 'hover:shadow-lg hover:bg-accent/5': getUserReaction(selectedPost, currentUserId) !== reactionType }"
                            >
                                <!-- Hover background animation -->
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 group-hover:animate-pulse rounded-lg"></div>
                                
                                <!-- Content -->
                                <div class="relative flex items-center gap-2 transition-transform duration-200 group-hover:scale-110">
                                    <span class="text-lg">{{ getReactionEmoji(reactionType) }}</span>
                                    <span class="hidden group-hover:inline-block whitespace-nowrap">{{ reactionType.charAt(0).toUpperCase() + reactionType.slice(1) }}</span>
                                </div>
                            </button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
