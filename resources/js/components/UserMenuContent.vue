<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';
import { inject, type Ref } from 'vue';

interface Props {
    user: User;
}

interface ModalState {
    isOpen: boolean;
    title: string;
    message: string;
}

const logoutModal = inject<Ref<ModalState>>('logoutModal');

const handleLogout = (e: Event) => {
    e.preventDefault();
    if (logoutModal) {
        logoutModal.value.isOpen = true;
    }
    // Modal shows while logout request is processing
    router.post(logout());
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <button
            class="block w-full px-2 py-1.5 text-left text-sm text-foreground hover:bg-accent hover:text-accent-foreground rounded-md"
            @click="handleLogout"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4 inline" />
            Log out
        </button>
    </DropdownMenuItem>
</template>
