<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import UserInfo from '@/components/UserInfo.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, FileText, User, Zap, Trophy, Star, Gift, MessageSquare, TrendingUp, Target, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth.user;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Courses',
        href: '/courses',
        icon: BookOpen,
    },
    {
        title: 'Quests',
        href: '/quests',
        icon: Target,
    },
    {
        title: 'Leaderboard',
        href: '/leaderboard',
        icon: Trophy,
    },
    {
        title: 'Achievements',
        href: '/achievements',
        icon: Star,
    },
    {
        title: 'Progress',
        href: '/progress',
        icon: TrendingUp,
    },
    {
        title: 'Rewards',
        href: '/rewards',
        icon: Gift,
    },
    {
        title: 'Community',
        href: '/community',
        icon: Users,
    },
    {
        title: 'Assignments',
        href: '/assignments',
        icon: FileText,
    },
    {
        title: 'Profile',
        href: '/profile',
        icon: User,
    },
];

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
                <SidebarMenuItem class="mt-4 px-2">
                    <div class="flex flex-col items-center gap-3 px-2 py-4 rounded-lg border border-sidebar-border">
                        <div class="flex-shrink-0 h-16 w-16 rounded-full overflow-hidden">
                            <img 
                                v-if="user.profile_photo_path"
                                :src="`/storage/${user.profile_photo_path}`" 
                                :alt="user.name"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="h-full w-full rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-lg font-bold">
                                {{ user.name.split(' ').map(n => n[0]).join('') }}
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-sm truncate">{{ user.name }}</p>
                        </div>
                    </div>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
