<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
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
            welcome_message: 'Welcome to the EduDream Administration Panel',
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
            welcome_message: 'مرحباً بك في لوحة إدارة منصة إيدو دريم',
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
        <!-- Debug Info -->
        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 rounded">
            <p class="text-sm text-yellow-800">Debug: User roles = {{ user.roles?.join(', ') || 'No roles' }}</p>
            <p class="text-sm text-yellow-800">Debug: Current locale = {{ currentLocale }}</p>
        </div>

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
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
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
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('recent_activity') }}</h2>
                        <button class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">{{ t('view_all') }}</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.message" class="flex items-center space-x-4 rtl:space-x-reverse p-4 rounded-lg hover:bg-gray-50 transition-colors">
                            <div :class="[
                                'flex items-center justify-center h-10 w-10 rounded-full',
                                activity.type === 'user_registered' ? 'bg-blue-100 text-blue-600' :
                                activity.type === 'course_published' ? 'bg-emerald-100 text-emerald-600' :
                                activity.type === 'enrollment' ? 'bg-purple-100 text-purple-600' :
                                'bg-orange-100 text-orange-600'
                            ]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="activity.icon === 'user-plus'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    <path v-else-if="activity.icon === 'book'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    <path v-else-if="activity.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ activity.message }}</p>
                                <p class="text-xs text-gray-500">{{ activity.time }}</p>
                            </div>
                        </div>
                        
                        <div v-if="!recentActivities || recentActivities.length === 0" class="text-center py-8">
                            <p class="text-gray-500">{{ currentLocale === 'ar' ? 'لا توجد أنشطة حديثة' : 'No recent activities' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('quick_actions') }}</h2>
                    
                    <div class="space-y-3">
                        <Link :href="route('admin.users.create')" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            {{ t('add_user') }}
                        </Link>

                        <Link :href="route('admin.courses.create')" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            {{ t('create_course') }}
                        </Link>

                        <Link :href="route('admin.users.index')" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                            </svg>
                            {{ currentLocale === 'ar' ? 'إدارة المستخدمين' : 'Manage Users' }}
                        </Link>

                        <Link :href="route('admin.courses.index')" class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            {{ currentLocale === 'ar' ? 'إدارة الكورسات' : 'Manage Courses' }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
