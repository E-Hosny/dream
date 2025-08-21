<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const isRTL = computed(() => currentLocale.value === 'ar');
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

// Helper function to check if user has role
const hasRole = (role) => {
    return userRoles.value.includes(role);
};

const showingSidebar = ref(true);
const showingMobileMenu = ref(false);

const switchLanguage = (locale) => {
    router.visit(`/language/${locale}`, {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
        replace: true,
    });
};

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            dashboard: 'Dashboard',
            users: 'Users',
            teachers: 'Teachers',
            students: 'Students',
            courses: 'Courses',
            lessons: 'Lessons',
            reports: 'Reports',
            settings: 'Settings',
            profile: 'Profile',
            logout: 'Logout',
            language: 'Language',
            search: 'Search...',
            notifications: 'Notifications',
            admin_panel: 'Admin Panel',
            welcome_back: 'Welcome back',
            system_management: 'System Management',
            academic_management: 'Academic Management',
        },
        ar: {
            dashboard: 'لوحة التحكم',
            users: 'المستخدمون',
            teachers: 'المعلمون',
            students: 'الطلاب',
            courses: 'الكورسات',
            lessons: 'الدروس',
            reports: 'التقارير',
            settings: 'الإعدادات',
            profile: 'الملف الشخصي',
            logout: 'تسجيل الخروج',
            language: 'اللغة',
            search: 'بحث...',
            notifications: 'الإشعارات',
            admin_panel: 'لوحة الإدارة',
            welcome_back: 'مرحباً بعودتك',
            system_management: 'إدارة النظام',
            academic_management: 'الإدارة الأكاديمية',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const adminMenuItems = [
    {
        title: 'dashboard',
        icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
        route: 'admin.dashboard',
        active: 'admin.dashboard'
    },
    {
        title: 'system_management',
        isHeader: true
    },
    {
        title: 'users',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z',
        route: 'admin.users.index',
        active: 'admin.users.*'
    },
    {
        title: 'teachers',
        icon: 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
        route: 'admin.teachers.index',
        active: 'admin.teachers.*'
    },
    {
        title: 'students',
        icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        route: 'admin.students.index',
        active: 'admin.students.*'
    },
    {
        title: 'academic_management',
        isHeader: true
    },
    {
        title: 'courses',
        icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        route: 'admin.courses.index',
        active: 'admin.courses.*'
    },
    {
        title: 'reports',
        icon: 'M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z',
        route: 'admin.reports.index',
        active: 'admin.reports.*'
    },
    {
        title: 'settings',
        icon: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        route: 'admin.settings.index',
        active: 'admin.settings.*'
    }
];
</script>

<template>
    <div class="flex h-screen bg-gray-50" :dir="isRTL ? 'rtl' : 'ltr'">
        <!-- Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 z-50 flex w-64 flex-col transition-transform duration-300 ease-in-out lg:translate-x-0',
                isRTL ? 'right-0' : 'left-0',
                showingSidebar ? 'translate-x-0' : (isRTL ? 'translate-x-64' : '-translate-x-64')
            ]"
        >
            <!-- Sidebar content -->
            <div class="flex flex-col overflow-y-auto bg-gradient-to-b from-indigo-900 via-indigo-800 to-indigo-900 pb-4 shadow-xl">
                <!-- Logo -->
                <div class="flex h-16 flex-shrink-0 items-center px-4 bg-indigo-950/50">
                    <div class="flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-400 to-indigo-400 shadow-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3">
                            <h1 class="text-sm font-bold text-white">{{ currentLocale === 'en' ? 'EduDream' : 'إيدو دريم' }}</h1>
                            <p class="text-xs text-indigo-200">{{ t('admin_panel') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-5 flex-1 space-y-1 px-3">
                    <template v-for="item in adminMenuItems" :key="item.title">
                        <!-- Header -->
                        <div v-if="item.isHeader" class="px-3 py-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-200">
                                {{ t(item.title) }}
                            </h3>
                        </div>
                        
                        <!-- Menu Item -->
                        <Link 
                            v-else
                            :href="route(item.route)"
                            :class="[
                                'group flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200',
                                route().current(item.active)
                                    ? 'bg-indigo-700/50 text-white shadow-md ring-1 ring-indigo-400/30'
                                    : 'text-indigo-200 hover:bg-indigo-700/30 hover:text-white'
                            ]"
                        >
                            <svg 
                                :class="[
                                    'flex-shrink-0 h-5 w-5 transition-colors',
                                    isRTL ? 'ml-3' : 'mr-3',
                                    route().current(item.active) ? 'text-white' : 'text-indigo-400 group-hover:text-white'
                                ]" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ t(item.title) }}
                        </Link>
                    </template>
                </nav>

                <!-- User Profile -->
                <div class="mt-auto border-t border-indigo-700/50 pt-4 px-3">
                    <div class="flex items-center px-3 py-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 text-sm font-semibold text-white">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                            <p class="text-xs text-indigo-200 truncate">{{ user.email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div :class="['flex flex-1 flex-col overflow-hidden', showingSidebar ? (isRTL ? 'mr-64' : 'ml-64') : '']">
            <!-- Top navigation -->
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button
                        @click="showingSidebar = !showingSidebar"
                        class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 lg:hidden"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Search -->
                    <div class="relative ml-4 rtl:ml-0 rtl:mr-4">
                        <div class="pointer-events-none absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-3 rtl:pl-0 rtl:pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            :placeholder="t('search')"
                            class="block w-80 rounded-lg border-gray-300 pl-10 rtl:pl-4 rtl:pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                </div>

                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                    <!-- Language Switcher -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1">
                        <button
                            @click="switchLanguage('en')"
                            :class="[
                                'px-3 py-1 text-sm rounded-md transition-all duration-200 font-medium',
                                currentLocale === 'en' 
                                    ? 'bg-white text-indigo-600 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            EN
                        </button>
                        <button
                            @click="switchLanguage('ar')"
                            :class="[
                                'px-3 py-1 text-sm rounded-md transition-all duration-200 font-medium',
                                currentLocale === 'ar' 
                                    ? 'bg-white text-indigo-600 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            عربي
                        </button>
                    </div>

                    <!-- Notifications -->
                    <button class="relative rounded-full p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z" />
                        </svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-red-400"></span>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative">
                        <button
                            @click="showingMobileMenu = !showingMobileMenu"
                            class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 text-sm font-semibold text-white">
                                {{ user.name.charAt(0) }}
                            </div>
                            <span class="ml-2 rtl:ml-0 rtl:mr-2 text-sm font-medium text-gray-700">{{ user.name }}</span>
                            <svg class="ml-1 rtl:ml-0 rtl:mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div 
                            v-show="showingMobileMenu"
                            class="absolute right-0 rtl:right-auto rtl:left-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ t('profile') }}
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                {{ t('logout') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for sidebar */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>
