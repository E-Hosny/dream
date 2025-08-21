<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            teacher_dashboard: 'Teacher Dashboard',
            welcome_message: 'Welcome to your teaching portal',
            my_courses_overview: 'My Courses Overview',
            active_courses: 'Active Courses',
            total_students: 'Total Students',
            pending_assignments: 'Pending Assignments',
            this_month_earnings: 'This Month Earnings',
            recent_student_activity: 'Recent Student Activity',
            upcoming_classes: 'Upcoming Classes',
            course_performance: 'Course Performance',
            view_all: 'View All',
            manage: 'Manage',
            quick_actions: 'Quick Actions',
            create_lesson: 'Create New Lesson',
            grade_assignments: 'Grade Assignments',
            view_analytics: 'View Analytics',
            schedule_class: 'Schedule Class',
            student_progress: 'Student Progress',
            top_performing_courses: 'Top Performing Courses',
            completion_rate: 'Completion Rate',
            student_rating: 'Student Rating',
            enrolled_students: 'Enrolled Students',
            new_message: 'New message from',
            submitted_assignment: 'submitted assignment',
            completed_lesson: 'completed lesson',
            minutes_ago: 'minutes ago',
            hour_ago: 'hour ago',
            hours_ago: 'hours ago',
        },
        ar: {
            teacher_dashboard: 'لوحة تحكم المعلم',
            welcome_message: 'مرحباً بك في بوابة التدريس',
            my_courses_overview: 'نظرة عامة على كورساتي',
            active_courses: 'الكورسات النشطة',
            total_students: 'إجمالي الطلاب',
            pending_assignments: 'الواجبات المعلقة',
            this_month_earnings: 'أرباح هذا الشهر',
            recent_student_activity: 'نشاط الطلاب الأخير',
            upcoming_classes: 'الفصول القادمة',
            course_performance: 'أداء الكورسات',
            view_all: 'عرض الكل',
            manage: 'إدارة',
            quick_actions: 'الإجراءات السريعة',
            create_lesson: 'إنشاء درس جديد',
            grade_assignments: 'تقييم الواجبات',
            view_analytics: 'عرض التحليلات',
            schedule_class: 'جدولة فصل',
            student_progress: 'تقدم الطلاب',
            top_performing_courses: 'أفضل الكورسات أداءً',
            completion_rate: 'معدل الإكمال',
            student_rating: 'تقييم الطلاب',
            enrolled_students: 'الطلاب المسجلين',
            new_message: 'رسالة جديدة من',
            submitted_assignment: 'سلم واجب',
            completed_lesson: 'أكمل درس',
            minutes_ago: 'منذ دقائق',
            hour_ago: 'منذ ساعة',
            hours_ago: 'منذ ساعات',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// Sample data - في التطبيق الحقيقي ستأتي من الخادم
const stats = {
    activeCourses: 8,
    totalStudents: 245,
    pendingAssignments: 12,
    monthlyEarnings: 3250
};

const recentActivity = [
    { 
        type: 'message', 
        student: 'سارة أحمد', 
        action: 'new_message', 
        course: 'Advanced Mathematics',
        time: '15 minutes_ago',
        avatar: 'SA'
    },
    { 
        type: 'assignment', 
        student: 'محمد علي', 
        action: 'submitted_assignment', 
        course: 'Physics 101',
        time: '1 hour_ago',
        avatar: 'MA'
    },
    { 
        type: 'completion', 
        student: 'فاطمة حسن', 
        action: 'completed_lesson', 
        course: 'Chemistry Basics',
        time: '2 hours_ago',
        avatar: 'FH'
    },
];

const topCourses = [
    {
        title: 'Advanced Mathematics',
        students: 45,
        completion: 87,
        rating: 4.8,
        earnings: '$1,250'
    },
    {
        title: 'Physics 101',
        students: 38,
        completion: 92,
        rating: 4.9,
        earnings: '$980'
    },
    {
        title: 'Chemistry Basics',
        students: 52,
        completion: 78,
        rating: 4.6,
        earnings: '$1,420'
    }
];

const upcomingClasses = [
    { time: '10:00 AM', course: 'Advanced Mathematics', students: 25, duration: '1h 30m' },
    { time: '2:00 PM', course: 'Physics 101', students: 18, duration: '1h' },
    { time: '4:00 PM', course: 'Chemistry Lab', students: 15, duration: '2h' },
];
</script>

<template>
    <Head :title="t('teacher_dashboard')" />

    <TeacherLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('teacher_dashboard') }}</h1>
            <p class="text-gray-600">{{ t('welcome_message') }}</p>
            <div class="mt-4 text-sm text-gray-500">
                {{ currentLocale === 'en' ? 'Good morning, Professor' : 'صباح الخير، أستاذ' }} {{ user.name }}
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Active Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.activeCourses }}</h3>
                        <p class="text-sm text-gray-500">{{ t('active_courses') }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.totalStudents }}</h3>
                        <p class="text-sm text-gray-500">{{ t('total_students') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Assignments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.pendingAssignments }}</h3>
                        <p class="text-sm text-gray-500">{{ t('pending_assignments') }}</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Earnings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">${{ stats.monthlyEarnings.toLocaleString() }}</h3>
                        <p class="text-sm text-gray-500">{{ t('this_month_earnings') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Student Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Activity Feed -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('recent_student_activity') }}</h2>
                        <button class="text-sm text-emerald-600 hover:text-emerald-500 font-medium">{{ t('view_all') }}</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div v-for="activity in recentActivity" :key="`${activity.student}-${activity.time}`" class="flex items-center space-x-4 rtl:space-x-reverse p-4 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 text-white text-sm font-semibold">
                                {{ activity.avatar }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    <span class="font-semibold">{{ activity.student }}</span>
                                    {{ currentLocale === 'en' ? activity.action.replace('_', ' ') : t(activity.action) }}
                                    {{ currentLocale === 'en' ? 'in' : 'في' }}
                                    <span class="font-medium text-emerald-600">{{ activity.course }}</span>
                                </p>
                                <p class="text-xs text-gray-500">{{ t(activity.time.replace(' ', '_')) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Courses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('top_performing_courses') }}</h2>
                        <button class="text-sm text-emerald-600 hover:text-emerald-500 font-medium">{{ t('view_all') }}</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div v-for="course in topCourses" :key="course.title" class="flex items-center justify-between p-4 rounded-lg border border-gray-100 hover:border-emerald-200 transition-colors">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ course.title }}</h3>
                                <div class="flex items-center space-x-4 rtl:space-x-reverse mt-2 text-sm text-gray-500">
                                    <span>{{ course.students }} {{ t('enrolled_students') }}</span>
                                    <span>{{ course.completion }}% {{ t('completion_rate') }}</span>
                                    <span>⭐ {{ course.rating }}</span>
                                </div>
                            </div>
                            <div class="text-right rtl:text-left">
                                <p class="font-semibold text-emerald-600">{{ course.earnings }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('quick_actions') }}</h2>
                    
                    <div class="space-y-3">
                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ t('create_lesson') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path>
                            </svg>
                            {{ t('grade_assignments') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"></path>
                            </svg>
                            {{ t('view_analytics') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"></path>
                            </svg>
                            {{ t('schedule_class') }}
                        </button>
                    </div>
                </div>

                <!-- Upcoming Classes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('upcoming_classes') }}</h2>
                    
                    <div class="space-y-4">
                        <div v-for="class_item in upcomingClasses" :key="class_item.course" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div>
                                <p class="font-medium text-gray-900">{{ class_item.course }}</p>
                                <p class="text-xs text-gray-500">{{ class_item.students }} {{ currentLocale === 'en' ? 'students' : 'طالب' }} • {{ class_item.duration }}</p>
                            </div>
                            <div class="text-right rtl:text-left">
                                <p class="text-sm font-semibold text-emerald-600">{{ class_item.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
