<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');
const user = computed(() => page.props.auth.user);

defineProps({
    stats: Object,
    recentActivities: Array
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            admin_dashboard: 'Admin Dashboard',
            welcome_message: 'Welcome to the inskola Administration Panel',
            overview: 'System Overview',
            total_users: 'Total Users',
            total_teachers: 'Total Teachers',
            total_students: 'Total Students',
            total_courses: 'Total Courses',
            recent_activity: 'Recent Activity',
            system_stats: 'System Statistics',
            user_registrations: 'User Registrations',
            course_enrollments: 'Course Enrollments',
            revenue: 'Revenue',
            view_all: 'View All',
            manage: 'Manage',
            quick_actions: 'Quick Actions',
            add_user: 'Add User',
            create_course: 'Create Course',
            view_reports: 'View Reports',
            system_settings: 'System Settings',
            activity_log: 'Activity Log',
            performance_metrics: 'Performance Metrics',
        },
        ar: {
            admin_dashboard: 'لوحة تحكم الإدارة',
            welcome_message: 'مرحباً بك في لوحة إدارة منصة إنسكولا',
            overview: 'نظرة عامة على النظام',
            total_users: 'إجمالي المستخدمين',
            total_teachers: 'إجمالي المعلمين',
            total_students: 'إجمالي الطلاب',
            total_courses: 'إجمالي الكورسات',
            recent_activity: 'النشاط الأخير',
            system_stats: 'إحصائيات النظام',
            user_registrations: 'تسجيلات المستخدمين',
            course_enrollments: 'التسجيل في الكورسات',
            revenue: 'الإيرادات',
            view_all: 'عرض الكل',
            manage: 'إدارة',
            quick_actions: 'الإجراءات السريعة',
            add_user: 'إضافة مستخدم',
            create_course: 'إنشاء كورس',
            view_reports: 'عرض التقارير',
            system_settings: 'إعدادات النظام',
            activity_log: 'سجل النشاط',
            performance_metrics: 'مقاييس الأداء',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// البيانات تأتي من الخادم عبر props
</script>

<template>
    <Head :title="t('admin_dashboard')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('admin_dashboard') }}</h1>
            <p class="text-gray-600">{{ t('welcome_message') }}</p>
            <div class="mt-4 text-sm text-gray-500">
                {{ currentLocale === 'en' ? 'Good morning,' : 'صباح الخير،' }} {{ user.name }}
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-brand to-brand-dark text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats?.totalUsers || 0 }}</h3>
                        <p class="text-sm text-gray-500">{{ t('total_users') }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Teachers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-brand to-brand-dark text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats?.totalTeachers || 0 }}</h3>
                        <p class="text-sm text-gray-500">{{ t('total_teachers') }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats?.totalStudents || 0 }}</h3>
                        <p class="text-sm text-gray-500">{{ t('total_students') }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats?.totalCourses || 0 }}</h3>
                        <p class="text-sm text-gray-500">{{ t('total_courses') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section - Center of the page -->
        <div class="mb-12">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ t('quick_actions') }}</h2>
                <p class="text-gray-600">{{ currentLocale === 'ar' ? 'إدارة منصة إيدو دريم بسهولة وسرعة' : 'Manage EduDream platform easily and quickly' }}</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Add User Card -->
                <Link :href="route('admin.users.create')" class="group">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mx-auto mb-6 group-hover:from-indigo-600 group-hover:to-purple-700 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ t('add_user') }}</h3>
                        <p class="text-sm text-gray-500">{{ currentLocale === 'ar' ? 'إضافة مستخدم جديد للنظام' : 'Add a new user to the system' }}</p>
                    </div>
                </Link>

                <!-- Create Course Card -->
                <Link :href="route('admin.courses.create')" class="group">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 bg-gradient-to-br from-brand to-brand-dark rounded-2xl mx-auto mb-6 group-hover:from-brand-dark group-hover:to-brand transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ t('create_course') }}</h3>
                        <p class="text-sm text-gray-500">{{ currentLocale === 'ar' ? 'إنشاء كورس جديد للمنصة' : 'Create a new course for the platform' }}</p>
                    </div>
                </Link>

                <!-- Manage Users Card -->
                <Link :href="route('admin.users.index')" class="group">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 bg-gradient-to-br from-brand to-brand-light rounded-2xl mx-auto mb-6 group-hover:from-brand-dark group-hover:to-brand transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ currentLocale === 'ar' ? 'إدارة المستخدمين' : 'Manage Users' }}</h3>
                        <p class="text-sm text-gray-500">{{ currentLocale === 'ar' ? 'عرض وإدارة جميع المستخدمين' : 'View and manage all users' }}</p>
                    </div>
                </Link>

                <!-- Manage Courses Card -->
                <Link :href="route('admin.courses.index')" class="group">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <div class="flex items-center justify-center h-16 w-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl mx-auto mb-6 group-hover:from-orange-600 group-hover:to-red-700 transition-all duration-300">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ currentLocale === 'ar' ? 'إدارة الكورسات' : 'Manage Courses' }}</h3>
                        <p class="text-sm text-gray-500">{{ currentLocale === 'ar' ? 'عرض وإدارة جميع الكورسات' : 'View and manage all courses' }}</p>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Recent Activity Section - Small section at bottom -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ t('recent_activity') }}</h2>
                <button class="text-sm text-brand hover:text-brand-dark font-medium transition-colors">{{ t('view_all') }}</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="activity in (recentActivities || []).slice(0, 6)" :key="activity.message" class="flex items-center space-x-3 rtl:space-x-reverse p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                    <div :class="[
                        'flex items-center justify-center h-8 w-8 rounded-full flex-shrink-0',
                        activity.type === 'user_registered' ? 'bg-brand/10 text-brand' :
                        activity.type === 'course_published' ? 'bg-green-100 text-green-600' :
                        activity.type === 'enrollment' ? 'bg-brand/10 text-brand' :
                        'bg-orange-100 text-orange-600'
                    ]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="activity.icon === 'user-plus'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            <path v-else-if="activity.icon === 'book'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            <path v-else-if="activity.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ activity.message }}</p>
                        <p class="text-xs text-gray-500">{{ activity.time }}</p>
                    </div>
                </div>
                
                <div v-if="!recentActivities || recentActivities.length === 0" class="col-span-full text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">{{ currentLocale === 'ar' ? 'لا توجد أنشطة حديثة' : 'No recent activities' }}</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
