<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');
const isRTL = computed(() => currentLocale.value === 'ar');
const user = computed(() => page.props.auth.user);
const userRoles = computed(() => user.value?.roles || []);

const showingSidebar = ref(false);
const showingMobileMenu = ref(false);

// Notification Management
const notifications = ref([]);
const unreadCount = ref(0);
const showingNotifications = ref(false);
const notificationLoading = ref(false);

const switchLanguage = (locale) => {
    router.visit(`/language/${locale}`, {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
        replace: true,
    });
};

// Notification Functions
const fetchNotifications = async () => {
    try {
        notificationLoading.value = true;
        const response = await axios.get('/notifications/recent?limit=5');
        if (response.data.success) {
            notifications.value = response.data.notifications;
            unreadCount.value = response.data.unread_count;
        }
    } catch (error) {
        console.error('Error fetching notifications:', error);
    } finally {
        notificationLoading.value = false;
    }
};

const markAsRead = async (notificationId) => {
    try {
        const response = await axios.post(`/notifications/${notificationId}/mark-as-read`);
        if (response.data.success) {
            // Update local notification state
            const notification = notifications.value.find(n => n.id === notificationId);
            if (notification && !notification.is_read) {
                notification.is_read = true;
                notification.read_at = new Date().toISOString();
                unreadCount.value = Math.max(0, unreadCount.value - 1);
            }
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        const response = await axios.post('/notifications/mark-all-as-read');
        if (response.data.success) {
            notifications.value.forEach(notification => {
                notification.is_read = true;
                notification.read_at = new Date().toISOString();
            });
            unreadCount.value = 0;
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
};

const handleNotificationClick = async (notification) => {
    if (!notification.is_read) {
        await markAsRead(notification.id);
    }
    
    if (notification.action_url) {
        // If external URL, open in new tab
        if (notification.action_url.startsWith('http')) {
            window.open(notification.action_url, '_blank');
        } else {
            // Internal URL, navigate with router
            router.visit(notification.action_url);
        }
    }
    
    showingNotifications.value = false;
};

const getNotificationIcon = (type) => {
    const icons = {
        assignment_created: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        assignment_submitted: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        assignment_corrected: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        default: 'M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z'
    };
    return icons[type] || icons.default;
};

const getNotificationColor = (color) => {
    const colors = {
        blue: 'text-brand',
        green: 'text-green-600',
        purple: 'text-purple-600',
        orange: 'text-orange-600',
        red: 'text-red-600',
        default: 'text-gray-600'
    };
    return colors[color] || colors.default;
};

// Auto-refresh notifications every 30 seconds
let notificationInterval;

// Handle responsive sidebar
const handleResize = () => {
    if (window.innerWidth >= 1024) { // lg breakpoint
        showingSidebar.value = true;
    } else {
        showingSidebar.value = false;
    }
};

onMounted(() => {
    fetchNotifications();
    notificationInterval = setInterval(fetchNotifications, 30000);
    
    // Set initial sidebar state based on screen size
    handleResize();
    
    // Add resize listener
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
    
    // Remove resize listener
    window.removeEventListener('resize', handleResize);
});

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
            mark_all_read: 'Mark all as read',
            loading: 'Loading',
            no_notifications: 'No notifications yet',
        },
        ar: {
            dashboard: 'لوحة التحكم',
            my_courses: 'الدروس',
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
            mark_all_read: 'وضع علامة مقروء على الكل',
            loading: 'جاري التحميل',
            no_notifications: 'لا توجد إشعارات بعد',
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
    <div class="flex min-h-screen bg-gradient-to-br from-gray-50 via-brand/10 to-brand-light/10" :dir="isRTL ? 'rtl' : 'ltr'">
        <!-- Mobile Sidebar Overlay -->
        <div 
            v-if="showingSidebar" 
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="showingSidebar = false"
        ></div>

        <!-- Sidebar -->
        <div 
            :class="[
                'fixed inset-y-0 z-50 flex w-64 flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
                isRTL ? 'right-0' : 'left-0',
                showingSidebar ? 'translate-x-0' : (isRTL ? 'translate-x-64' : '-translate-x-64')
            ]"
        >
            <!-- Sidebar content -->
            <div class="flex flex-col h-full overflow-y-auto bg-white shadow-2xl border-r border-gray-200">
                <!-- Logo -->
                <div class="flex h-20 flex-shrink-0 items-center justify-center px-4 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-center w-full">
                        <img src="/200-600 out icon gr -- EH.png" alt="Inskola Logo" class="h-16 w-auto object-contain" />
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-5 flex-1 space-y-1 px-3">
                    <template v-for="item in teacherMenuItems" :key="item.title">
                        <!-- Header -->
                        <div v-if="item.isHeader" class="px-3 py-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">
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
                                    ? 'bg-brand text-white shadow-md ring-1 ring-brand/30'
                                    : 'text-gray-700 hover:bg-brand/10 hover:text-brand'
                            ]"
                        >
                            <svg 
                                :class="[
                                    'flex-shrink-0 h-5 w-5 transition-colors',
                                    isRTL ? 'ml-3' : 'mr-3',
                                    route().current(item.active) ? 'text-white' : 'text-gray-500 group-hover:text-brand'
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
                <div class="mt-auto border-t border-gray-200 pt-4 px-3">
                    <div class="flex items-center px-3 py-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-brand to-brand-light text-sm font-semibold text-white">
                            {{ user.name.charAt(0) }}
                        </div>
                        <div class="ml-3 rtl:ml-0 rtl:mr-3 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                            <p class="text-xs text-gray-600 truncate">{{ user.email }}</p>
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
                        class="rounded-md p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand lg:hidden"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Search -->
                    <div class="relative ml-4 rtl:ml-0 rtl:mr-4 hidden sm:block">
                        <div class="pointer-events-none absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-3 rtl:pl-0 rtl:pr-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            :placeholder="t('search')"
                            class="block w-full max-w-xs lg:max-w-md xl:max-w-lg rounded-lg border-gray-300 pl-10 rtl:pl-4 rtl:pr-10 text-sm focus:border-brand focus:ring-brand"
                        >
                    </div>
                    
                    <!-- Mobile Search Button -->
                    <button class="sm:hidden ml-2 rtl:ml-0 rtl:mr-2 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4 rtl:space-x-reverse">
                    <!-- Notifications -->
                    <div class="relative">
                        <button 
                            @click="showingNotifications = !showingNotifications"
                            class="relative rounded-full p-2 text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z" />
                            </svg>
                            <span 
                                v-if="unreadCount > 0" 
                                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white font-medium"
                            >
                                {{ unreadCount > 9 ? '9+' : unreadCount }}
                            </span>
                        </button>

                        <!-- Notifications Dropdown -->
                        <div 
                            v-if="showingNotifications"
                            class="z-50 max-h-96 overflow-y-auto bg-white shadow-lg border border-gray-200
                                   fixed inset-x-0 top-16 w-full
                                   sm:absolute sm:inset-x-auto sm:top-auto sm:mt-2 sm:w-96 sm:rounded-lg"
                            :class="[
                                isRTL 
                                    ? 'sm:left-4 sm:right-auto' 
                                    : 'sm:right-4'
                            ]"
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between p-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">{{ t('notifications') }}</h3>
                                <div class="flex items-center space-x-2" :class="isRTL ? 'space-x-reverse' : ''">
                                    <button
                                        v-if="unreadCount > 0"
                                        @click="markAllAsRead"
                                        class="text-xs text-brand hover:text-brand-dark font-medium"
                                    >
                                        {{ t('mark_all_read') }}
                                    </button>
                                    <button
                                        @click="showingNotifications = false"
                                        class="text-gray-400 hover:text-gray-600"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Loading State -->
                            <div v-if="notificationLoading" class="p-4 text-center">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand mx-auto"></div>
                                <p class="text-sm text-gray-500 mt-2">{{ t('loading') }}...</p>
                            </div>

                            <!-- Notifications List -->
                            <div v-else-if="notifications.length > 0" class="divide-y divide-gray-100">
                                <div 
                                    v-for="notification in notifications" 
                                    :key="notification.id"
                                    @click="handleNotificationClick(notification)"
                                    class="p-4 hover:bg-gray-50 cursor-pointer transition-colors duration-200"
                                    :class="!notification.is_read ? 'bg-brand/5' : ''"
                                >
                                    <div class="flex items-start space-x-3" :class="isRTL ? 'space-x-reverse' : ''">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                                 :class="!notification.is_read ? 'bg-brand/10' : 'bg-gray-100'">
                                                <svg class="w-4 h-4" :class="getNotificationColor(notification.color)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getNotificationIcon(notification.type)" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900" :class="!notification.is_read ? 'font-semibold' : ''">
                                                {{ notification.title }}
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                                {{ notification.message }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ notification.time_ago }}
                                            </p>
                                        </div>
                                        <div v-if="!notification.is_read" class="w-2 h-2 bg-brand rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-else class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zm-4-5l4-3 4 3v5h-8v-5z" />
                                </svg>
                                <p class="mt-4 text-sm">{{ t('no_notifications') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Language Switcher -->
                    <div class="hidden md:flex items-center bg-gray-100 rounded-lg p-1">
                        <button
                            @click="switchLanguage('en')"
                            :class="[
                                'px-3 py-1 text-sm rounded-md transition-all duration-200 font-medium',
                                currentLocale === 'en' 
                                    ? 'bg-white text-brand shadow-sm' 
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
                                    ? 'bg-white text-brand shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            عربي
                        </button>
                    </div>

                    <!-- Mobile Language Switcher -->
                    <button 
                        class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand"
                        @click="switchLanguage(currentLocale === 'ar' ? 'en' : 'ar')"
                    >
                        <span class="text-xs font-medium">{{ currentLocale === 'ar' ? 'EN' : 'ع' }}</span>
                    </button>

                    <!-- Profile dropdown -->
                    <div class="relative">
                        <button
                            @click="showingMobileMenu = !showingMobileMenu"
                            class="flex items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-brand to-brand-light text-sm font-semibold text-white">
                                {{ user.name.charAt(0) }}
                            </div>
                            <span class="hidden sm:block ml-2 rtl:ml-0 rtl:mr-2 text-sm font-medium text-gray-700 max-w-32 truncate">{{ user.name }}</span>
                            <svg class="hidden sm:block ml-1 rtl:ml-0 rtl:mr-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div 
                            v-show="showingMobileMenu"
                            :class="[
                                'absolute mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50',
                                isRTL 
                                    ? 'left-0 sm:right-0 sm:left-auto origin-top-left sm:origin-top-right max-w-[calc(100vw-1rem)] sm:max-w-none' 
                                    : 'left-0 origin-top-left max-w-[calc(100vw-1rem)] sm:max-w-none'
                            ]"
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
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6">
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
    background: rgba(0, 0, 0, 0.05);
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
</style>
