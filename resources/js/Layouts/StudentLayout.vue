<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const isRTL = computed(() => currentLocale.value === 'ar');
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

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
            my_courses: 'My Courses',
            browse_courses: 'Browse Courses',
            lessons: 'Lessons',
            assignments: 'Assignments',
            grades: 'My Grades',
            certificates: 'Certificates',
            progress: 'Progress',
            profile: 'Profile',
            logout: 'Logout',
            language: 'Language',
            search: 'Search...',
            notifications: 'Notifications',
            student_portal: 'Student Portal',
            welcome_back: 'Welcome back',
            learning: 'Learning',
            achievements: 'Achievements',
            schedule: 'Schedule',
            library: 'Library',
            discussion: 'Discussion',
            help: 'Help & Support',
        },
        ar: {
            dashboard: 'لوحة التحكم',
            my_courses: 'كورساتي',
            browse_courses: 'تصفح الكورسات',
            lessons: 'الدروس',
            assignments: 'الواجبات',
            grades: 'درجاتي',
            certificates: 'الشهادات',
            progress: 'التقدم',
            profile: 'الملف الشخصي',
            logout: 'تسجيل الخروج',
            language: 'اللغة',
            search: 'بحث...',
            notifications: 'الإشعارات',
            student_portal: 'بوابة الطالب',
            welcome_back: 'مرحباً بعودتك',
            learning: 'التعلم',
            achievements: 'الإنجازات',
            schedule: 'الجدول',
            library: 'المكتبة',
            discussion: 'المناقشة',
            help: 'المساعدة والدعم',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const studentMenuItems = [
    {
        title: 'dashboard',
        icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
        route: 'student.dashboard',
        active: 'student.dashboard'
    },
    {
        title: 'learning',
        isHeader: true
    },
    {
        title: 'my_courses',
        icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        route: 'student.courses.index',
        active: 'student.courses.*'
    },
    {
        title: 'browse_courses',
        icon: 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z',
        route: 'student.courses.browse',
        active: 'student.courses.browse'
    },
    {
        title: 'lessons',
        icon: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        route: 'student.lessons.index',
        active: 'student.lessons.*'
    },
    {
        title: 'assignments',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        route: 'student.assignments.index',
        active: 'student.assignments.*'
    },
    {
        title: 'schedule',
        icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5',
        route: 'student.schedule.index',
        active: 'student.schedule.*'
    },
    {
        title: 'achievements',
        isHeader: true
    },
    {
        title: 'grades',
        icon: 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75',
        route: 'student.grades.index',
        active: 'student.grades.*'
    },
    {
        title: 'progress',
        icon: 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        route: 'student.progress.index',
        active: 'student.progress.*'
    },
    {
        title: 'certificates',
        icon: 'M10.125 2.25a4.125 4.125 0 100 8.25 4.125 4.125 0 000-8.25zM23.25 8.25a4.125 4.125 0 11-8.25 0 4.125 4.125 0 018.25 0z M23.25 19.125a4.125 4.125 0 11-8.25 0 4.125 4.125 0 018.25 0zM10.125 12.75a4.125 4.125 0 100 8.25 4.125 4.125 0 000-8.25z',
        route: 'student.certificates.index',
        active: 'student.certificates.*'
    },
    {
        title: 'library',
        icon: 'M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25H10.69l-2.12-2.12z',
        route: 'student.library.index',
        active: 'student.library.*'
    },
    {
        title: 'help',
        icon: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z',
        route: 'student.help.index',
        active: 'student.help.*'
    }
];
</script>

<template>
    <div class="flex min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-cyan-50/50" :dir="isRTL ? 'rtl' : 'ltr'">
        <!-- Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 z-50 flex w-64 flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                isRTL ? 'right-0' : 'left-0',
                showingSidebar ? 'translate-x-0' : (isRTL ? 'translate-x-64' : '-translate-x-64')
            ]"
        >
            <!-- Sidebar content -->
            <div class="flex flex-col h-full overflow-y-auto bg-gradient-to-b from-blue-900 via-blue-800 to-blue-900 shadow-2xl">
                <!-- Logo -->
                <div class="flex h-16 flex-shrink-0 items-center px-4 bg-blue-950/50">
                    <div class="flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-400 to-cyan-400 shadow-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3">
                            <h1 class="text-sm font-bold text-white">{{ currentLocale === 'en' ? 'EduDream' : 'إيدو دريم' }}</h1>
                            <p class="text-xs text-blue-200">{{ t('student_portal') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-5 flex-1 space-y-1 px-3">
                    <template v-for="item in studentMenuItems" :key="item.title">
                        <!-- Header -->
                        <div v-if="item.isHeader" class="px-3 py-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-blue-200">
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
                                    ? 'bg-blue-700/50 text-white shadow-md ring-1 ring-blue-400/30'
                                    : 'text-blue-200 hover:bg-blue-700/30 hover:text-white'
                            ]"
                        >
                            <svg 
                                :class="[
                                    'flex-shrink-0 h-5 w-5 transition-colors',
                                    isRTL ? 'ml-3' : 'mr-3',
                                    route().current(item.active) ? 'text-white' : 'text-blue-400 group-hover:text-white'
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
                <div class="mt-auto border-t border-blue-700/50 pt-4 px-3">
                    <div class="flex items-center px-3 py-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-purple-400 to-pink-400 text-sm font-semibold text-white">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                            <p class="text-xs text-blue-200 truncate">{{ user.email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-1 flex-col min-h-screen lg:ml-0"
             :class="[showingSidebar && 'lg:ml-0']">
            <!-- Top navigation -->
            <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button
                        @click="showingSidebar = !showingSidebar"
                        class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden"
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
                            class="block w-80 rounded-lg border-gray-300 pl-10 rtl:pl-4 rtl:pr-10 text-sm focus:border-blue-500 focus:ring-blue-500"
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
                                    ? 'bg-white text-blue-600 shadow-sm' 
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
                                    ? 'bg-white text-blue-600 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            عربي
                        </button>
                    </div>

                    <!-- Notifications -->
                    <button class="relative rounded-full p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z" />
                        </svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-blue-400"></span>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative">
                        <button
                            @click="showingMobileMenu = !showingMobileMenu"
                            class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-cyan-600 text-sm font-semibold text-white">
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
