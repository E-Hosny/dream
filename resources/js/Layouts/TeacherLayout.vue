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
            create_course: 'Create Course',
            lessons: 'Lessons',
            students: 'My Students',
            assessments: 'Assessments',
            grades: 'Grades',
            reports: 'Reports',
            profile: 'Profile',
            logout: 'Logout',
            language: 'Language',
            search: 'Search...',
            notifications: 'Notifications',
            teacher_portal: 'Teacher Portal',
            welcome_back: 'Welcome back',
            course_management: 'Course Management',
            student_management: 'Student Management',
            schedule: 'Schedule',
            calendar: 'Calendar',
        },
        ar: {
            dashboard: 'لوحة التحكم',
            my_courses: 'كورساتي',
            create_course: 'إنشاء كورس',
            lessons: 'الدروس',
            students: 'طلابي',
            assessments: 'التقييمات',
            grades: 'الدرجات',
            reports: 'التقارير',
            profile: 'الملف الشخصي',
            logout: 'تسجيل الخروج',
            language: 'اللغة',
            search: 'بحث...',
            notifications: 'الإشعارات',
            teacher_portal: 'بوابة المعلم',
            welcome_back: 'مرحباً بعودتك',
            course_management: 'إدارة الكورسات',
            student_management: 'إدارة الطلاب',
            schedule: 'الجدول',
            calendar: 'التقويم',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const teacherMenuItems = [
    {
        title: 'dashboard',
        icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
        route: 'teacher.dashboard',
        active: 'teacher.dashboard'
    },
    {
        title: 'course_management',
        isHeader: true
    },
    {
        title: 'my_courses',
        icon: 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25',
        route: 'teacher.courses.index',
        active: 'teacher.courses.*'
    },
    {
        title: 'lessons',
        icon: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        route: 'teacher.lessons.index',
        active: 'teacher.lessons.*'
    },
    {
        title: 'assessments',
        icon: 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z',
        route: 'teacher.assessments.index',
        active: 'teacher.assessments.*'
    },
    {
        title: 'student_management',
        isHeader: true
    },
    {
        title: 'students',
        icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        route: 'teacher.students.index',
        active: 'teacher.students.*'
    },
    {
        title: 'grades',
        icon: 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75',
        route: 'teacher.grades.index',
        active: 'teacher.grades.*'
    },
    {
        title: 'reports',
        icon: 'M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z',
        route: 'teacher.reports.index',
        active: 'teacher.reports.*'
    },
    {
        title: 'calendar',
        icon: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5',
        route: 'teacher.calendar.index',
        active: 'teacher.calendar.*'
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
            <div class="flex flex-col overflow-y-auto bg-gradient-to-b from-emerald-900 via-emerald-800 to-emerald-900 pb-4 shadow-xl">
                <!-- Logo -->
                <div class="flex h-16 flex-shrink-0 items-center px-4 bg-emerald-950/50">
                    <div class="flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-400 to-teal-400 shadow-lg">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3">
                            <h1 class="text-sm font-bold text-white">{{ currentLocale === 'en' ? 'EduDream' : 'إيدو دريم' }}</h1>
                            <p class="text-xs text-emerald-200">{{ t('teacher_portal') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-5 flex-1 space-y-1 px-3">
                    <template v-for="item in teacherMenuItems" :key="item.title">
                        <!-- Header -->
                        <div v-if="item.isHeader" class="px-3 py-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-emerald-200">
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
                                    ? 'bg-emerald-700/50 text-white shadow-md ring-1 ring-emerald-400/30'
                                    : 'text-emerald-200 hover:bg-emerald-700/30 hover:text-white'
                            ]"
                        >
                            <svg 
                                :class="[
                                    'flex-shrink-0 h-5 w-5 transition-colors',
                                    isRTL ? 'ml-3' : 'mr-3',
                                    route().current(item.active) ? 'text-white' : 'text-emerald-400 group-hover:text-white'
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
                <div class="mt-auto border-t border-emerald-700/50 pt-4 px-3">
                    <div class="flex items-center px-3 py-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-amber-400 to-orange-400 text-sm font-semibold text-white">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ user.name }}</p>
                            <p class="text-xs text-emerald-200 truncate">{{ user.email }}</p>
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
                        class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 lg:hidden"
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
                            class="block w-80 rounded-lg border-gray-300 pl-10 rtl:pl-4 rtl:pr-10 text-sm focus:border-emerald-500 focus:ring-emerald-500"
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
                                    ? 'bg-white text-emerald-600 shadow-sm' 
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
                                    ? 'bg-white text-emerald-600 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            عربي
                        </button>
                    </div>

                    <!-- Notifications -->
                    <button class="relative rounded-full p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z" />
                        </svg>
                        <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-orange-400"></span>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative">
                        <button
                            @click="showingMobileMenu = !showingMobileMenu"
                            class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 text-sm font-semibold text-white">
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
