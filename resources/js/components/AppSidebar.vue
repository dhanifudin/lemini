<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { AppPageProps, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, Folder, LayoutGrid, MessageSquare, Target, UserRound } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<AppPageProps>();

const role = computed(() => page.props.auth.user?.role ?? 'student');

const mainNavItems = computed<NavItem[]>(() => {
    if (role.value === 'teacher') {
        return [
            {
                title: 'Teacher Dashboard',
                href: '/teacher/dashboard',
                icon: LayoutGrid,
            },
            {
                title: 'Feedback Queue',
                href: '/teacher/dashboard#queue',
                icon: MessageSquare,
            },
        ];
    }

    return [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Feedback',
            href: '/feedback',
            icon: MessageSquare,
        },
        {
            title: 'Practice',
            href: '/practice',
            icon: Target,
        },
        {
            title: 'Profile',
            href: '/profile',
            icon: UserRound,
        },
    ];
});

const homeHref = computed(() => (role.value === 'teacher' ? '/teacher/dashboard' : '/dashboard'));

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeHref">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
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
